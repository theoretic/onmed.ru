import "./styles.css";
import { ApiError } from "./api/client";
import { fetchAllDoctors, fetchDoctor } from "./api/doctor";
import { fetchSchedule } from "./api/schedule";
import { fetchAllSpecialities, fetchSpecialities } from "./api/speciality";
import { addMonths, hasAvailableDayInMonth } from "./lib/date";
import { h, mount } from "./lib/dom";
import { Store, createInitial } from "./state";
import type { AppState } from "./types";
import { renderCalendar } from "./views/calendar";
import { renderDaySlots } from "./views/daySlots";
import { renderHeader } from "./views/header";
import { createSpecCombo, type SpecCombo } from "./views/specCombo";

export class AppointmentSpecialistsAll extends HTMLElement {
  private _store!: Store;
  private _abort?: AbortController;
  private _specCombo?: SpecCombo;

  connectedCallback(): void {
    const apiBase = `${window.location.origin}/api/medflex`;
    this._store = new Store(createInitial(apiBase));
    this._renderShell();
    this._store.subscribe(() => this._renderAll());
    this._loadInitialData();
  }

  disconnectedCallback(): void {
    this._abort?.abort();
    this._specCombo?.destroy();
    this._specCombo = undefined;
  }

  private _renderShell(): void {
    // combo slot lives outside body so re-rendering body doesn't detach the
    // combobox (which would lose focus/typing state).
    this.replaceChildren(
      h("div", { "data-slot": "header" }),
      h("div", { "data-slot": "combo" }),
      h("div", { "data-slot": "body" }),
    );
    this._renderAll();
  }

  private async _loadInitialData(): Promise<void> {
    this._abort?.abort();
    this._abort = new AbortController();
    this._store.set({ phase: "loading" });
    try {
      const signal = this._abort.signal;
      const [specialities, doctors] = await Promise.all([
        fetchAllSpecialities(this._store.state.apiBase, signal),
        fetchAllDoctors(this._store.state.apiBase, signal),
      ]);
      this._store.set({ phase: "ready", specialities, doctors });
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

      // Auto-select service matching selected speciality (or first available)
      const specId = this._store.state.selectedSpecialityId;
      const autoService = specId
        ? schedule.services.find((svc) => Number(svc.id) === specId)
        : schedule.services[0];
      this._store.set({ phase: "ready", schedule, partialWarning: scheduleResult.warning, selectedServiceId: autoService?.id });

      // If current visible month has no free days but the next month does,
      // jump the calendar forward once so the user lands on a useful view.
      const cur = this._store.state.visibleMonth;
      const next = addMonths(cur, 1);
      if (!hasAvailableDayInMonth(schedule.days, cur) && hasAvailableDayInMonth(schedule.days, next)) {
        this._store.set({ visibleMonth: next });
      }
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

    if (s.phase === "loading" && !s.specialities) {
      mount(headerSlot, null);
      mount(this.querySelector('[data-slot="combo"]'), null);
      mount(bodySlot, h("div", { class: "as-skeleton loading" }, "Загрузка..."));
      return;
    }
    if (s.phase === "error") {
      mount(headerSlot, null);
      mount(this.querySelector('[data-slot="combo"]'), null);
      mount(bodySlot, h("div", { class: "as-error-banner" }, s.errorMsg || "Ошибка"));
      return;
    }

    mount(headerSlot, s.schedule ? renderHeader(s.schedule.doctor) : null);
    this._renderCombo(s);
    mount(bodySlot, this._renderBody(s));
  }

  private _renderCombo(s: AppState): void {
    const slot = this.querySelector('[data-slot="combo"]');
    if (!slot || !s.specialities || !s.doctors) return;
    const doctorSpecIds = new Set(s.doctors.flatMap((d) => d.specialityIds));
    const available = s.specialities.filter((sp) => doctorSpecIds.has(sp.id));
    if (!this._specCombo) {
      this._specCombo = createSpecCombo({
        onSelect: (id) => this._store.set({
          selectedSpecialityId: id,
          selectedDoctorId: undefined,
          selectedServiceId: undefined,
          selectedDate: undefined,
          selectedSlotISO: undefined,
          schedule: undefined,
          partialWarning: undefined,
        }),
      });
      const label = h("label", { class: "as-select-label" }, "Специализация");
      label.append(this._specCombo.el);
      mount(slot, label);
    }
    this._specCombo.update(available, s.selectedSpecialityId);
  }

  private _renderBody(s: AppState): HTMLElement {
    const body = h("div", { class: "as-col" });

    if (!s.selectedSpecialityId) return body;

    // Step 2: Doctor select filtered by selected speciality
    if (s.doctors) {
      const filtered = s.doctors.filter((d) => d.specialityIds.includes(s.selectedSpecialityId!));
      const docLabel = h("label", { class: "as-select-label" }, "Врач");
      const docSelect = h("select", { class: "as-doctor-select" }) as HTMLSelectElement;
      const placeholder = h("option", { value: "" }, "") as HTMLOptionElement;
      placeholder.disabled = true;
      placeholder.selected = !s.selectedDoctorId;
      docSelect.append(placeholder);
      docLabel.append(docSelect);

      const FMT = new Intl.NumberFormat("ru-RU");
      const formatDur = (min: number) => min < 60 ? `${min} мин` : `${Math.floor(min / 60)} ч${min % 60 ? ` ${min % 60} мин` : ''}`;
      for (const doc of filtered) {
        let label = doc.name;
        if (doc.id === s.selectedDoctorId && s.schedule) {
          const svc = s.selectedSpecialityId
            ? s.schedule.services.find((sv) => Number(sv.id) === s.selectedSpecialityId)
            : s.schedule.services[0];
          if (svc) label = `${doc.name} — ${FMT.format(svc.price)} ₽, ${formatDur(svc.durationMin)}`;
        }
        const opt = h("option", { value: doc.id }, label) as HTMLOptionElement;
        if (doc.id === s.selectedDoctorId) opt.selected = true;
        docSelect.append(opt);
      }

      docSelect.addEventListener("change", () => {
        const id = docSelect.value;
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

      body.append(docLabel);
    }

    // Loading spinner while fetching doctor data (after doctor selection)
    if (s.phase === "loading" && s.doctors) {
      body.append(h("div", { class: "as-skeleton loading" }, "Загрузка..."));
      return body;
    }

    if (!s.schedule || !s.selectedDoctorId) return body;

    const sched = s.schedule;

    if (s.partialWarning) {
      body.append(h("div", { class: "as-warning" }, s.partialWarning));
    }

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

    // Resolve doctor name from the loaded doctors list by selectedDoctorId
    // so the coupon shows the canonical name, not the API schedule name.
    const doctorInList = s.doctors?.find((d) => d.id === s.selectedDoctorId);
    const specialityInList = s.specialities?.find((sp) => sp.id === s.selectedSpecialityId);
    const formAttrs: Record<string, string> = {
      "doctor-id": sched.doctor.id,
      "doctor-name": doctorInList?.name ?? sched.doctor.name,
      "doctor-speciality": specialityInList?.name ?? "",
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
