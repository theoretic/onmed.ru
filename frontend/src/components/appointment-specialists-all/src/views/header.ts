import { h } from "../lib/dom";
import type { Doctor } from "../types";

export function renderHeader(doctor: Doctor): HTMLElement {
  return h("div", { class: "as-hdr" });
}
