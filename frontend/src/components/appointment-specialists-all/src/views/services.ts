import { h } from "../lib/dom";
import type { Service } from "../types";

const FMT = new Intl.NumberFormat("ru-RU");

function formatDuration(min: number): string {
  if (min < 60) return `${min} мин`;
  const hr = Math.floor(min / 60);
  const m = min % 60;
  return m === 0 ? `${hr} ч` : `${hr} ч ${m} мин`;
}

export function renderServices(
  services: Service[],
  selectedId: string | undefined,
  onSelect: (id: string) => void,
): HTMLElement {
  if (!services.length) {
    return h("div", { class: "as-muted" }, "Нет доступных услуг.");
  }
  return h(
    "div",
    { class: "as-srv-list" },
    h("h6", {}, "Выберите специализацию"),
    ...services.map((s) =>
      h(
        "div",
        {
          class: `as-srv as-item${s.id === selectedId ? " as-item--selected" : ""}`,
          onclick: () => onSelect(s.id),
        },
        h(
          "div",
          {},
          h("div", {}, s.name),
          h("div", { class: "as-meta" }, formatDuration(s.durationMin)),
        ),
        h("div", { class: "as-price nowrap" }, `${FMT.format(s.price)} ₽`),
      ),
    ),
  );
}
