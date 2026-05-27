// Custom combobox replacement for SlimSelect.
// Owns its DOM and local UI state (open/query/highlight).
// Commits only on selection — typing does NOT touch the Store.

import { h } from "../lib/dom";
import type { Speciality } from "../types";

export interface SpecCombo {
  el: HTMLElement;
  update(available: Speciality[], selectedId?: number): void;
  destroy(): void;
}

export function createSpecCombo(opts: { onSelect: (id: number) => void }): SpecCombo {
  let items: Speciality[] = [];
  let selectedId: number | undefined;
  let filtered: Speciality[] = [];
  let query = "";
  let open = false;
  let highlight = -1;

  const listId = `as-combo-${Math.random().toString(36).slice(2, 8)}`;

  const input = h("input", {
    type: "text",
    class: "as-combo-input",
    autocomplete: "off",
    spellcheck: false,
    placeholder: "Введите или выберите специализацию",
    "aria-autocomplete": "list",
    "aria-controls": listId,
  }) as HTMLInputElement;

  const toggle = h("button", {
    type: "button",
    class: "as-combo-toggle",
    tabindex: "-1",
    "aria-label": "Открыть список",
  }) as HTMLButtonElement;

  const list = h("ul", {
    id: listId,
    class: "as-combo-list",
    role: "listbox",
    hidden: true,
  }) as HTMLUListElement;

  // Hidden native <select> for form autofill / screen readers / no-JS fallback.
  const native = h("select", {
    class: "as-combo-native",
    "aria-hidden": "true",
    tabindex: "-1",
  }) as HTMLSelectElement;

  const root = h("div", {
    class: "as-combo",
    role: "combobox",
    "aria-haspopup": "listbox",
    "aria-expanded": "false",
  });
  root.append(input, toggle, list, native);

  const nameFor = (id: number) => items.find((s) => s.id === id)?.name ?? "";

  function applyFilter(): void {
    const q = query.trim().toLowerCase();
    filtered = q ? items.filter((s) => s.name.toLowerCase().includes(q)) : items.slice();
    if (filtered.length === 0) highlight = -1;
    else if (highlight >= filtered.length) highlight = filtered.length - 1;
    else if (highlight < 0) highlight = 0;
  }

  function renderList(): void {
    list.replaceChildren();
    if (filtered.length === 0) {
      list.append(h("li", { class: "as-combo-empty" }, "Не найдено"));
      input.removeAttribute("aria-activedescendant");
      return;
    }
    const q = query.trim().toLowerCase();
    filtered.forEach((sp, i) => {
      const li = h("li", {
        id: `${listId}-${sp.id}`,
        class: "as-combo-opt"
          + (i === highlight ? " is-highlighted" : "")
          + (sp.id === selectedId ? " is-selected" : ""),
        role: "option",
        "aria-selected": sp.id === selectedId ? "true" : "false",
        "data-id": String(sp.id),
      });
      if (q) {
        const lower = sp.name.toLowerCase();
        const idx = lower.indexOf(q);
        if (idx >= 0) {
          li.append(
            document.createTextNode(sp.name.slice(0, idx)),
            h("mark", {}, sp.name.slice(idx, idx + q.length)),
            document.createTextNode(sp.name.slice(idx + q.length)),
          );
        } else {
          li.textContent = sp.name;
        }
      } else {
        li.textContent = sp.name;
      }
      list.append(li);
    });
    if (highlight >= 0) {
      input.setAttribute("aria-activedescendant", `${listId}-${filtered[highlight].id}`);
    } else {
      input.removeAttribute("aria-activedescendant");
    }
  }

  function scrollHighlightIntoView(): void {
    if (highlight < 0) return;
    (list.children[highlight] as HTMLElement | undefined)?.scrollIntoView({ block: "nearest" });
  }

  function setOpen(o: boolean): void {
    if (open === o) return;
    open = o;
    root.setAttribute("aria-expanded", String(open));
    list.hidden = !open;
    if (open) {
      applyFilter();
      renderList();
      scrollHighlightIntoView();
      document.addEventListener("pointerdown", onDocPointer, true);
    } else {
      document.removeEventListener("pointerdown", onDocPointer, true);
    }
  }

  function commit(id: number): void {
    selectedId = id;
    input.value = nameFor(id);
    query = "";
    native.value = String(id);
    setOpen(false);
    opts.onSelect(id);
  }

  function onDocPointer(e: Event): void {
    if (!root.contains(e.target as Node)) setOpen(false);
  }

  input.addEventListener("input", () => {
    query = input.value;
    if (!open) setOpen(true);
    else { applyFilter(); renderList(); scrollHighlightIntoView(); }
  });
  input.addEventListener("focus", () => {
    if (!open) { query = ""; setOpen(true); input.select(); }
  });
  input.addEventListener("keydown", (e: KeyboardEvent) => {
    switch (e.key) {
      case "ArrowDown":
        e.preventDefault();
        if (!open) { setOpen(true); break; }
        highlight = Math.min(highlight + 1, filtered.length - 1);
        renderList(); scrollHighlightIntoView();
        break;
      case "ArrowUp":
        e.preventDefault();
        if (!open) { setOpen(true); break; }
        highlight = Math.max(highlight - 1, 0);
        renderList(); scrollHighlightIntoView();
        break;
      case "Home":
        if (!open) break;
        e.preventDefault();
        highlight = 0; renderList(); scrollHighlightIntoView();
        break;
      case "End":
        if (!open) break;
        e.preventDefault();
        highlight = filtered.length - 1; renderList(); scrollHighlightIntoView();
        break;
      case "Enter":
        if (!open || highlight < 0) break;
        e.preventDefault();
        commit(filtered[highlight].id);
        break;
      case "Escape":
        if (open) { e.preventDefault(); setOpen(false); }
        break;
      case "Tab":
        setOpen(false);
        break;
    }
  });
  toggle.addEventListener("click", (e) => {
    e.preventDefault();
    if (open) { setOpen(false); return; }
    query = "";
    setOpen(true);
    // Don't steal focus on touch: avoids popping the virtual keyboard.
  });
  list.addEventListener("mousedown", (e) => {
    // Prevent input blur before click fires.
    e.preventDefault();
  });
  list.addEventListener("click", (e) => {
    e.preventDefault(); // prevent wrapping <label> ancestor from refocusing input
    const li = (e.target as HTMLElement).closest(".as-combo-opt") as HTMLElement | null;
    if (!li) { setOpen(false); return; }
    const id = Number(li.dataset.id);
    if (id) commit(id);
  });
  list.addEventListener("pointermove", (e) => {
    const li = (e.target as HTMLElement).closest(".as-combo-opt") as HTMLElement | null;
    if (!li) return;
    const idx = Array.prototype.indexOf.call(list.children, li);
    if (idx !== highlight) { highlight = idx; renderList(); }
  });
  native.addEventListener("change", () => {
    const id = Number(native.value);
    if (id && id !== selectedId) commit(id);
  });

  return {
    el: root,
    update(available, selId) {
      items = available;
      selectedId = selId;
      // Rebuild hidden native options.
      native.replaceChildren();
      const ph = h("option", { value: "" }) as HTMLOptionElement;
      ph.disabled = true;
      ph.selected = !selectedId;
      native.append(ph);
      for (const sp of items) {
        const opt = h("option", { value: String(sp.id) }, sp.name) as HTMLOptionElement;
        if (sp.id === selectedId) opt.selected = true;
        native.append(opt);
      }
      // Don't clobber in-progress typing.
      if (document.activeElement !== input) {
        input.value = selectedId ? nameFor(selectedId) : "";
        query = "";
      }
      if (open) { applyFilter(); renderList(); }
    },
    destroy() {
      setOpen(false);
    },
  };
}
