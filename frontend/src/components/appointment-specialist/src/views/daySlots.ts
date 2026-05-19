import { buildSlots, timeHM, parseLocalMs } from "../lib/date";
import { h } from "../lib/dom";
import type { DaySchedule, Service } from "../types";

function intersectsBooked(startISO: string, endMs: number, booked: [string, string][]): boolean {
  const startMs = parseLocalMs(startISO);
  for (const [bs, be] of booked) {
    const bsMs = parseLocalMs(bs);
    const beMs = parseLocalMs(be);
    if (startMs < beMs && endMs > bsMs) return true;
  }
  return false;
}

export function renderDaySlots(
  day: DaySchedule,
  service: Service,
  selectedSlotISO: string | undefined,
  onPick: (iso: string) => void,
): HTMLElement {
  const step = service.durationMin;
  const stepMs = step * 60_000;
  const all: { iso: string; free: boolean }[] = [];
  for (const [s, e] of day.freeIntervals) {
    for (const iso of buildSlots(s, e, step)) {
      const endMs = parseLocalMs(iso) + stepMs;
      const free = !intersectsBooked(iso, endMs, day.bookedIntervals);
      all.push({ iso, free });
    }
  }
  if (!all.length) {
    return h(
      "div",
      { class: "as-slots flex-1" },
      h("h6", {}, "Выберите время"),
      h("div", { class: "as-muted" }, "Нет свободных интервалов."),
    );
  }
  return h(
    "div",
    { class: "as-slots flex-1" },
    h("h6", {}, "Выберите время"),
    h(
      "div",
      { class: "as-slot-flex" },
      ...all.map((s) => {
        let cls = "as-slot";
        if (!s.free) cls += " as-slot--disabled";
        else if (s.iso === selectedSlotISO) cls += " as-item as-item--selected";
        else cls += " as-item";
        return h(
          "div",
          { class: cls, onclick: s.free ? () => onPick(s.iso) : null },
          timeHM(s.iso),
        );
      }),
    ),
  );
}
