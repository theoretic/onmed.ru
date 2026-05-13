import type { Schedule } from "../types";
import { request } from "./client";
import { type RawScheduleResp, normalizeSchedule } from "./normalize";

export interface ScheduleResult {
  schedule: Schedule;
  warning?: string;
}

export async function fetchSchedule(
  base: string,
  doctorId: string,
  signal?: AbortSignal,
): Promise<ScheduleResult> {
  const raw = await request<RawScheduleResp & { warning?: string }>(base, "/schedule/", {
    query: { doctor_id: doctorId },
    signal,
  });
  return {
    schedule: normalizeSchedule(raw, doctorId),
    warning: raw.warning,
  };
}
