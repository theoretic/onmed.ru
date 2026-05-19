// Date utilities (UTC-safe, local-day grid).

export const RU_MONTHS = [
  "Январь",
  "Февраль",
  "Март",
  "Апрель",
  "Май",
  "Июнь",
  "Июль",
  "Август",
  "Сентябрь",
  "Октябрь",
  "Ноябрь",
  "Декабрь",
];

export const RU_WEEKDAYS_SHORT = ["Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Вс"];

export function startOfDay(d: Date): Date {
  return new Date(d.getFullYear(), d.getMonth(), d.getDate());
}

export function startOfMonth(d: Date): Date {
  return new Date(d.getFullYear(), d.getMonth(), 1);
}

export function addMonths(d: Date, n: number): Date {
  return new Date(d.getFullYear(), d.getMonth() + n, 1);
}

export function isoDate(d: Date): string {
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, "0");
  const day = String(d.getDate()).padStart(2, "0");
  return `${y}-${m}-${day}`;
}

export function sameMonth(a: Date, b: Date): boolean {
  return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth();
}

// Build 6x7 grid (Mon-first) for given month.
export function monthGrid(month: Date): Date[] {
  const first = startOfMonth(month);
  // Mon=0..Sun=6
  const offset = (first.getDay() + 6) % 7;
  const start = new Date(first.getFullYear(), first.getMonth(), 1 - offset);
  const cells: Date[] = [];
  for (let i = 0; i < 42; i++) {
    cells.push(new Date(start.getFullYear(), start.getMonth(), start.getDate() + i));
  }
  return cells;
}

// Parse "YYYY-MM-DD HH:MM" or "YYYY-MM-DDTHH:MM" as LOCAL time (milliseconds).
// Safari strictly follows ECMAScript spec and treats new Date("YYYY-MM-DDTHH:MM")
// (no timezone designator) as UTC, while Chrome/Firefox treat it as local time.
// Using the Date(year, month, day, hours, minutes) constructor always creates
// a local-time Date regardless of browser.
export function parseLocalMs(iso: string): number {
  const [datePart, timePart = "00:00"] = iso.replace("T", " ").split(" ");
  const [y, mo, d] = datePart.split("-").map(Number);
  const [h = 0, min = 0] = timePart.split(":").map(Number);
  return new Date(y, mo - 1, d, h, min).getTime();
}

// Generate slots between [startISO, endISO) with stepMinutes.
export function buildSlots(startISO: string, endISO: string, stepMinutes: number): string[] {
  const out: string[] = [];
  if (stepMinutes <= 0) return out;
  const start = parseLocalMs(startISO);
  const end = parseLocalMs(endISO);
  if (!Number.isFinite(start) || !Number.isFinite(end) || start >= end) return out;
  const step = stepMinutes * 60_000;
  for (let t = start; t + step <= end; t += step) {
    const d = new Date(t);
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, "0");
    const dd = String(d.getDate()).padStart(2, "0");
    const hh = String(d.getHours()).padStart(2, "0");
    const min = String(d.getMinutes()).padStart(2, "0");
    out.push(`${yyyy}-${mm}-${dd} ${hh}:${min}`);
  }
  return out;
}

export function timeHM(iso: string): string {
  const d = new Date(parseLocalMs(iso));
  return `${String(d.getHours()).padStart(2, "0")}:${String(d.getMinutes()).padStart(2, "0")}`;
}
