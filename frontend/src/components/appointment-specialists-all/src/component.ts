import "./styles.css";
import { ApiError } from "./api/client";
import { fetchAllDoctors, fetchDoctor } from "./api/doctor";
import { fetchSchedule } from "./api/schedule";
import { fetchSpecialities } from "./api/speciality";
import { addMonths } from "./lib/date";
import { h, mount } from "./lib/dom";
import { Store, createInitial } from "./state";
import type { AppState } from "./types";
import { renderCalendar } from "./views/calendar";
import { renderDaySlots } from "./views/daySlots";
import { renderHeader } from "./views/header";
import { renderServices } from "./views/services";

export class AppointmentSpecialistsAll extends HTMLElement {
  private _store!: Store;
  private _abort?: AbortController;

  connectedCallback(): void {
    const apiBase = `${window.location.origin}/api/medflex`;
    this._store = new Store(createInitial(apiBase));
    this._renderShell();
    this._store.subscribe(() => this._renderAll());
    this._loadAllDoctors();
  }

  disconnectedCallback(): void {
    this._abort?.abort();
  }

  private _renderShell(): void {
    this.replaceChildren(h("div", { "data-slot": "header" }), h("div", { "data-slot": "body" }));
    this._renderAll();
  }

  private async _loadAllDoctors(): Promise<void> {
    this._abort?.abort();
    this._abort = new AbortController();
    this._store.set({ phase: "loading" });
    try {
      const doctors = await fetchAllDoctors(this._store.state.apiBase, this._abort.signal);
      this._store.set({ phase: "ready", doctors });
    } catch (err) {
      if ((err as { name?: string }).name === "AbortError") return;
      const msg = err instanceof ApiError ? `Ошибка ${err.status}` : "Ошибка загрузки";
      this._store.set({ phase: "error", errorMsg: msg });
    }
  }

  private async _loadDoctorData(doctorId: string): Promise<void> {
    this._abort?.abort();
    this._abort = new AbortController();
    this._store.set({ phase: "loading", schedule: undefined });
    try {
      const s = this._store.state;
      const signal = this._abort.signal;
      const doctor = await fetchDoctor(s.apiBase, signal, doctorId);
      const [allSpecialities, scheduleResult] = await Promise.all([
        fetchSpecialities(s.apiBase, doctor.id, signal),
        fetchSchedule(s.apiBase, doctor.id, signal),
      ]);
      const schedule = scheduleResult.schedule;

      if (doctor.specialityIds.length > 0) {
        const specMap = new Map(allSpecialities.map((sp) => [sp.id, sp.name]));
        schedule.services = schedule.services
          .filter((svc) => doctor.specialityIds.includes(Number(svc.id)))
          .map((svc) => ({ ...svc, name: specMap.get(Number(svc.id)) ?? svc.name }));
      }

      this._store.set({ phase: "ready", schedule, partialWarning: scheduleResult.warning });
    } catch (err) {
      if ((err as { name?: string }).name === "AbortError") return;
      const msg = err instanceof ApiError ? `Ошибка ${err.status}` : "Ошибка загрузки";
      this._store.set({ phase: "error", errorMsg: msg });
    }
  }

  private _renderAll(): void {
    const s = this._store.state;
    const headerSlot = this.querySelector('[data-slot="header"]');
    const bodySlot = this.querySelector('[data-slot="body"]');
    if (!headerSlot || !bodySlot) return;

    if (s.phase === "loading" && !s.doctors) {
      mount(headerSlot, null);
      mount(bodySlot, h("div", { class: "as-skeleton loading" }, "Загрузка..."));
      return;
    }
    if (s.phase === "error") {
      mount(headerSlot, null);
      mount(bodySlot, h("div", { class: "as-error-banner" }, s.errorMsg || "Ошибка"));
      return;
    }

    mount(headerSlot, s.schedule ? renderHeader(s.schedule.doctor) : null);
    mount(bodySlot, this._renderBody(s));
  }

  private _renderBody(s: AppState): HTMLElement {
    const body = h("div", { class: "as-col" });

    // Doctor dropdown — always shown once doctors list is loaded
    if (s.doctors) {
      const select = h("select", { class: "as-doctor-select" }) as HTMLSelectElement;
      const placeholder = h("option", { value: "" }, "— Выберите врача —") as HTMLOptionElement;
      placeholder.disabled = true;
      placeholder.selected = !s.selectedDoctorId;
      select.append(placeholder);

      for (const doc of s.doctors) {
        const opt = h("option", { value: doc.id }, doc.name) as HTMLOptionElement;
        if (doc.id === s.selectedDoctorId) opt.selected = true;
        select.append(opt);
      }

      select.addEventListener("change", () => {
        const id = select.value;
        if (!id) return;
        this._store.set({
          selectedDoctorId: id,
          selectedServiceId: undefined,
          selectedDate: undefined,
          selectedSlotISO: undefined,
          schedule: undefined,
          partialWarning: undefined,
        });
        this._loadDoctorData(id);
      });

      body.append(select);
    }

    // Loading spinner while fetching doctor data (after selection)
    if (s.phase === "loading" && s.doctors) {
      body.append(h("div", { class: "as-skeleton loading" }, "Загрузка..."));
      return body;
    }

    if (!s.schedule || !s.selectedDoctorId) return body;

    const sched = s.schedule;

    if (s.partialWarning) {
      body.append(h("div", { class: "as-warning" }, s.partialWarning));
    }

    body.append(
      renderServices(sched.services, s.selectedServiceId, (id) => {
        this._store.set({
          selectedServiceId: id,
          selectedDate: undefined,
          selectedSlotISO: undefined,
        });
      }),
    );

    if (!s.selectedServiceId) return body;
    const service = sched.services.find((x) => x.id === s.selectedServiceId);
    if (!service) return body;

    if (!s.selectedDate) {
      body.append(
        h("div", { class: "as-cal-row" },
          renderCalendar({
            visibleMonth: s.visibleMonth,
            days: sched.days,
            selectedDate: s.selectedDate,
            onPrev: () => this._store.set({ visibleMonth: addMonths(s.visibleMonth, -1) }),
            onNext: () => this._store.set({ visibleMonth: addMonths(s.visibleMonth, 1) }),
            onPick: (date) => this._store.set({ selectedDate: date, selectedSlotISO: undefined }),
          }),
        ),
      );
      return body;
    }

    const day = sched.days.find((d) => d.date === s.selectedDate);
    if (!day) {
      body.append(
        h("div", { class: "as-cal-row" },
          renderCalendar({
            visibleMonth: s.visibleMonth,
            days: sched.days,
            selectedDate: s.selectedDate,
            onPrev: () => this._store.set({ visibleMonth: addMonths(s.visibleMonth, -1) }),
            onNext: () => this._store.set({ visibleMonth: addMonths(s.visibleMonth, 1) }),
            onPick: (date) => this._store.set({ selectedDate: date, selectedSlotISO: undefined }),
          }),
        ),
      );
      body.append(h("div", { class: "as-muted" }, "Нет данных по выбранному дню."));
      return body;
    }

    body.append(
      h("div", { class: "as-cal-row" },
        renderCalendar({
          visibleMonth: s.visibleMonth,
          days: sched.days,
          selectedDate: s.selectedDate,
          onPrev: () => this._store.set({ visibleMonth: addMonths(s.visibleMonth, -1) }),
          onNext: () => this._store.set({ visibleMonth: addMonths(s.visibleMonth, 1) }),
          onPick: (date) => this._store.set({ selectedDate: date, selectedSlotISO: undefined }),
        }),
        renderDaySlots(day, service, s.selectedSlotISO, (iso) => {
          this._store.set({ selectedSlotISO: iso });
        }),
      ),
    );

    if (!s.selectedSlotISO) return body;

    const formAttrs: Record<string, string> = {
      "doctor-id": sched.doctor.id,
      "service-id": s.selectedServiceId,
      "price": String(service?.price ?? 0),
      "start-time": s.selectedSlotISO,
      "duration-min": String(service?.durationMin ?? 30),
    };
    if (service?.ageLimit) {
      formAttrs["age-min"] = String(service.ageLimit.min);
      formAttrs["age-max"] = String(service.ageLimit.max);
    }
    body.append(h("appointment-form", formAttrs));

    return body;
  }
}
