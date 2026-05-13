// Map raw Medflex API response into domain Schedule.
// API shape is loosely defined; we accept several common shapes and degrade gracefully.

import { isoDate } from "../lib/date";
import type { AgeLimit, DaySchedule, DayState, Schedule, Service } from "../types";

export interface RawDoctor {
  id?: string | number;
  full_name?: string;
  fio?: string;
  name?: string;
  services?: RawService[];
  schedule?: RawDay[];
  days?: RawDay[];
}

export interface RawService {
  id?: string | number;
  name?: string;
  title?: string;
  price?: number;
  cost?: number;
  duration?: number; // minutes
  duration_min?: number;
}

export interface RawInterval {
  start?: string;
  end?: string;
  from?: string;
  to?: string;
}

export interface RawDay {
  date?: string;
  free?: RawInterval[];
  free_intervals?: RawInterval[];
  busy?: RawInterval[];
  booked?: RawInterval[];
  booked_intervals?: RawInterval[];
}

export interface RawCell {
  dt_start: string; // "YYYY-MM-DD HH:MM"
  dt_end: string;
}

export interface RawScheduleEntry {
  doctor_id?: string | number;
  lpu_id?: string | number;
  specialities?: number[];
  prices?: { speciality_id: number; price: number | null }[];
  cells?: RawCell[];
}

export interface RawScheduleResp {
  // Old /v2/doctors_schedule format
  doctors?: RawDoctor[];
  // New /schedule/ format: data is array of entries with cells
  // Old format: data is RawDoctor[] or { doctors: RawDoctor[] }
  data?: RawDoctor[] | { doctors?: RawDoctor[] } | RawScheduleEntry[];
  count?: number;
  allowed_age?: { speciality_id: number; min: number; max: number }[];
}

function pickDoctor(resp: RawScheduleResp, doctorId: string): RawDoctor | undefined {
  const lists: RawDoctor[][] = [];
  if (Array.isArray(resp.doctors)) lists.push(resp.doctors);
  if (Array.isArray(resp.data)) lists.push(resp.data as RawDoctor[]);
  else if (resp.data && Array.isArray((resp.data as { doctors?: RawDoctor[] }).doctors)) {
    lists.push((resp.data as { doctors: RawDoctor[] }).doctors);
  }
  for (const list of lists) {
    const m = list.find((d) => String(d.id) === String(doctorId));
    if (m) return m;
    if (list.length === 1) return list[0];
  }
  return undefined;
}

function normService(s: RawService): Service {
  return {
    id: String(s.id ?? ""),
    name: s.name || s.title || "Услуга",
    price: Number(s.price ?? s.cost ?? 0),
    durationMin: Number(s.duration_min ?? s.duration ?? 30),
  };
}

function normInterval(i: RawInterval): [string, string] | null {
  const a = i.start || i.from;
  const b = i.end || i.to;
  if (!a || !b) return null;
  return [a, b];
}

function normDay(d: RawDay): DaySchedule {
  const free = (d.free || d.free_intervals || []).map(normInterval).filter(Boolean) as [
    string,
    string,
  ][];
  const booked = (d.busy || d.booked || d.booked_intervals || [])
    .map(normInterval)
    .filter(Boolean) as [string, string][];
  let state: DayState = "disabled";
  if (free.length > 0) state = booked.length > 0 ? "partial" : "free";
  return {
    date: d.date || "",
    state,
    freeIntervals: free,
    bookedIntervals: booked,
  };
}

function isNewScheduleFormat(resp: RawScheduleResp): boolean {
  return (
    Array.isArray(resp.data) &&
    resp.data.length > 0 &&
    typeof (resp.data as unknown[])[0] === "object" &&
    "cells" in ((resp.data as unknown[])[0] as object)
  );
}

function normalizeNewFormat(
  entries: RawScheduleEntry[],
  doctorId: string,
  doctorName: string,
  allowedAges: { speciality_id: number; min: number; max: number }[],
): Schedule {
  // Collect all cells for this doctor (filter only when doctor_id is present)
  const relevant = entries.filter(
    (e) => !e.doctor_id || String(e.doctor_id) === String(doctorId),
  );

  // Aggregate unique prices across LPU entries
  const priceMap = new Map<number, number>();
  for (const e of relevant) {
    for (const p of e.prices || []) {
      if (!priceMap.has(p.speciality_id)) {
        priceMap.set(p.speciality_id, Number(p.price ?? 0));
      }
    }
  }

  // Group cells by date → free intervals; derive slot duration from first cell
  const dayMap = new Map<string, [string, string][]>();
  let cellDurationMin = 0;
  for (const e of relevant) {
    for (const cell of e.cells || []) {
      if (!cell.dt_start || !cell.dt_end) continue;
      const date = cell.dt_start.slice(0, 10);
      if (!date) continue;
      // Replace space separator with T for spec-compliant ISO 8601 parsing
      const startISO = cell.dt_start.replace(" ", "T");
      const endISO = cell.dt_end.replace(" ", "T");
      if (cellDurationMin === 0) {
        const diffMin = (new Date(endISO).getTime() - new Date(startISO).getTime()) / 60_000;
        if (diffMin > 0) cellDurationMin = diffMin;
      }
      if (!dayMap.has(date)) dayMap.set(date, []);
      dayMap.get(date)!.push([startISO, endISO]);
    }
  }

  const ageLimitMap = new Map<number, AgeLimit>();
  for (const a of allowedAges) {
    ageLimitMap.set(a.speciality_id, { min: a.min, max: a.max });
  }

  const slotDuration = cellDurationMin > 0 ? cellDurationMin : 30;
  const services: Service[] =
    priceMap.size > 0
      ? Array.from(priceMap.entries()).map(([id, price]) => ({
          id: String(id),
          name: "Приём специалиста",
          price,
          durationMin: slotDuration,
          ageLimit: ageLimitMap.get(id),
        }))
      : [{ id: doctorId, name: "Приём специалиста", price: 0, durationMin: slotDuration }];

  const days: DaySchedule[] = Array.from(dayMap.entries())
    .sort(([a], [b]) => a.localeCompare(b))
    .map(([date, free]) => ({
      date,
      state: (free.length > 0 ? "free" : "disabled") as DayState,
      freeIntervals: free,
      bookedIntervals: [],
    }));

  return { doctor: { id: doctorId, name: doctorName, specialityIds: [] }, services, days };
}

export function normalizeSchedule(
  raw: RawScheduleResp,
  doctorId: string,
  doctorName = "Специалист",
): Schedule {
  // New /schedule/ API format
  if (isNewScheduleFormat(raw)) {
    return normalizeNewFormat(raw.data as RawScheduleEntry[], doctorId, doctorName, raw.allowed_age ?? []);
  }
  // Old /v2/doctors_schedule format
  const d = pickDoctor(raw, doctorId);
  const name = d?.full_name || d?.fio || d?.name || doctorName;
  const services = (d?.services || []).map(normService);
  const rawDays = d?.schedule || d?.days || [];
  const days = rawDays
    .map(normDay)
    .filter((x) => x.date)
    .map((x) => ({ ...x, date: x.date.length > 10 ? isoDate(new Date(x.date)) : x.date }));
  return {
    doctor: { id: doctorId, name, specialityIds: [] },
    services,
    days,
  };
}
