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

export async function fetchDoctor(
  base: string,
  signal?: AbortSignal,
): Promise<Doctor> {
  const raw = await request<RawDoctorResp>(base, "/doctor/", { signal });
  const entry = Array.isArray(raw.data) ? raw.data[0] : undefined;
  return {
    id: String(entry?.id ?? ""),
    name: entry?.efio || entry?.full_name || "",
    specialityIds: entry?.specialities ?? [],
  };
}
