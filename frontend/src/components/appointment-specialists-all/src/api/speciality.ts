import type { Speciality } from "../types";
import { request } from "./client";

interface RawSpecialityResp {
  data?: { id: number; name: string }[];
}

export async function fetchSpecialities(
  base: string,
  doctorId: string,
  signal?: AbortSignal,
): Promise<Speciality[]> {
  const raw = await request<RawSpecialityResp>(base, "/speciality/", {
    query: { doctor_id: doctorId },
    signal,
  });
  return Array.isArray(raw.data) ? raw.data.map((s) => ({ id: s.id, name: s.name })) : [];
}

export async function fetchAllSpecialities(
  base: string,
  signal?: AbortSignal,
): Promise<Speciality[]> {
  const raw = await request<RawSpecialityResp>(base, "/speciality/", { signal });
  return Array.isArray(raw.data)
    ? raw.data.map((s) => ({ id: s.id, name: s.name })).sort((a, b) => a.name.localeCompare(b.name, "ru"))
    : [];
}
