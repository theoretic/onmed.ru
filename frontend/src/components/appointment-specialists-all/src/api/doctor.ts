import type { Doctor } from "../types";
import { request } from "./client";

interface RawDoctorEntry {
  id?: number | string;
  efio?: string;
  full_name?: string;
  specialities?: number[];
}

interface RawDoctorResp {
  data?: RawDoctorEntry[];
}

function normalizeEntry(entry: RawDoctorEntry): Doctor {
  return {
    id: String(entry?.id ?? ""),
    name: entry?.efio || entry?.full_name || "",
    specialityIds: entry?.specialities ?? [],
  };
}

/** Fetch a single doctor. Pass doctorId to request ?doctor_id=X, otherwise falls back to page id_medflex. */
export async function fetchDoctor(
  base: string,
  signal?: AbortSignal,
  doctorId?: string,
): Promise<Doctor> {
  const path = doctorId ? `/doctor/?doctor_id=${encodeURIComponent(doctorId)}` : "/doctor/";
  const raw = await request<RawDoctorResp>(base, path, { signal });
  const entry = Array.isArray(raw.data) ? raw.data[0] : undefined;
  return normalizeEntry(entry ?? {});
}

/** Fetch all doctors from cached endpoint. Sorted by name. */
export async function fetchAllDoctors(
  base: string,
  signal?: AbortSignal,
): Promise<Doctor[]> {
  const raw = await request<RawDoctorResp>(base, "/doctor/?cached=1", { signal });
  return (raw.data ?? [])
    .map(normalizeEntry)
    .filter((d) => d.id && d.name)
    .sort((a, b) => a.name.localeCompare(b.name, "ru"));
}
