import {
  RU_MONTHS,
  RU_WEEKDAYS_SHORT,
  addMonths,
  isoDate,
  monthGrid,
  sameMonth,
  startOfDay,
  startOfMonth,
} from "../lib/date";
import { h } from "../lib/dom";
import type { DaySchedule } from "../types";

export interface CalendarOpts {
  visibleMonth: Date;
  days: DaySchedule[];
  selectedDate: string | undefined;
  onPrev: () => void;
  onNext: () => void;
  onPick: (date: string) => void;
}

export function renderCalendar(opts: CalendarOpts): HTMLElement {
  const today = startOfDay(new Date());
  const minMonth = startOfMonth(today);
  const maxMonth = addMonths(minMonth, 1);
  const cur = startOfMonth(opts.visibleMonth);

  const byDate = new Map(opts.days.map((d) => [d.date, d]));

  const cells = monthGrid(cur).map((d) => {
    const inMonth = sameMonth(d, cur);
    const iso = isoDate(d);
    const past = d.getTime() < today.getTime();
    const sched = byDate.get(iso);
    let cls = "as-day";
    if (!inMonth) cls += " as-day--out";
    if (!inMonth || past || !sched || sched.state === "disabled") {
      // not available — no modifier, no pointer events (handled by absence of as-item)
    } else {
      cls += " as-item as-day--available";
      if (sched.state === "partial") cls += " as-day--partial";
    }
    if (iso === opts.selectedDate) cls += " as-item--selected";

    const clickable = inMonth && !past && sched && sched.state !== "disabled";
    return h(
      "div",
      {
        class: cls,
        onclick: clickable ? () => opts.onPick(iso) : null,
      },
      String(d.getDate()),
    );
  });

  const canPrev = cur.getTime() > minMonth.getTime();
  const canNext = cur.getTime() < maxMonth.getTime();

  return h(
    "div",
    { class: "as-cal flex-1" },
    h("h6", {}, "Выберите дату"),
    h(
      "div",
      { class: "as-cal-wrapper" },
      h(
        "div",
        { class: "as-cal-nav" },
        h("button", { onclick: () => canPrev && opts.onPrev(), disabled: !canPrev }, "‹"),
        h("div", {}, `${RU_MONTHS[cur.getMonth()]} ${cur.getFullYear()}`),
        h("button", { onclick: () => canNext && opts.onNext(), disabled: !canNext }, "›"),
      ),
      h(
        "div",
        { class: "as-cal-grid" },
        ...RU_WEEKDAYS_SHORT.map((w) => h("div", { class: "as-cal-wd" }, w)),
        ...cells,
      ),
    ),
  );
}
