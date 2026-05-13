import "./styles.css";
import { ApiError } from "./api/client";
import { fetchDoctor } from "./api/doctor";
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

export class AppointmentSpecialist extends HTMLElement {
  private _store!: Store;
  private _abort?: AbortController;

  connectedCallback(): void {
    const apiBase = `${window.location.origin}/api/medflex`;

    this._store = new Store(createInitial(apiBase));
    this._renderShell();
    this._store.subscribe(() => this._renderAll());
    this._loadData();
  }

  disconnectedCallback(): void {
    this._abort?.abort();
  }

  private _renderShell(): void {
    this.replaceChildren(h("div", { "data-slot": "header" }), h("div", { "data-slot": "body" }));
    this._renderAll();
  }

  private async _loadData(): Promise<void> {
    this._abort?.abort();
    this._abort = new AbortController();
    this._store.set({ phase: "loading" });
    try {
      const s = this._store.state;
      const signal = this._abort.signal;
      const doctor = await fetchDoctor(s.apiBase, signal);
      const [allSpecialities, scheduleResult] = await Promise.all([
        fetchSpecialities(s.apiBase, doctor.id, signal),
        fetchSchedule(s.apiBase, doctor.id, signal),
      ]);
      const schedule = scheduleResult.schedule;

      // Strict: filter services to doctor's specialityIds and resolve names
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

    if (s.phase === "loading" || s.phase === "idle") {
      mount(headerSlot, null);
      mount(bodySlot, h("div", { class: "as-skeleton loading" }, "Загрузка..."));
      return;
    }
    if (s.phase === "error") {
      mount(headerSlot, null);
      mount(bodySlot, h("div", { class: "as-error-banner" }, s.errorMsg || "Ошибка"));
      return;
    }

    if (!s.schedule) return;
    mount(headerSlot, renderHeader(s.schedule.doctor));
    mount(bodySlot, this._renderBody(s));
  }

  private _renderBody(s: AppState): HTMLElement {
    const sched = s.schedule;
    if (!sched) return h("div");
    const body = h("div", { class: "as-col" });

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
        h(
          "div",
          { class: "as-cal-row" },
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
        h(
          "div",
          { class: "as-cal-row" },
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
      h(
        "div",
        { class: "as-cal-row" },
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
