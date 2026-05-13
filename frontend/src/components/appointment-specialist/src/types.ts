// API DTOs (loose) and domain types.

export type { AgeLimit } from '../../../js/lib/types';

export interface Speciality {
  id: number;
  name: string;
}

export interface Doctor {
  id: string;
  name: string; // ФИО
  specialityIds: number[];
}

export interface Service {
  id: string;
  name: string;
  price: number;
  durationMin: number; // step in minutes
  currency?: string;
  ageLimit?: AgeLimit;
}

export type DayState = "disabled" | "free" | "partial";
export type SlotState = "disabled" | "free";

export interface DaySchedule {
  date: string; // YYYY-MM-DD
  state: DayState;
  // Free intervals returned by API: array of [startISO, endISO]
  freeIntervals: [string, string][];
  // All booked intervals
  bookedIntervals: [string, string][];
}

export interface Schedule {
  doctor: Doctor;
  services: Service[];
  days: DaySchedule[];
}

export type Phase = "idle" | "loading" | "ready" | "error";

export interface AppState {
  phase: Phase;
  errorMsg?: string;
  partialWarning?: string;
  apiBase: string;
  schedule?: Schedule;
  selectedServiceId?: string;
  visibleMonth: Date; // first day of month being shown
  selectedDate?: string; // YYYY-MM-DD
  selectedSlotISO?: string;
}
