import { h } from "../../../js/lib/dom";
import type { AgeLimit } from "../../../js/lib/types";

function field(label: string, input: HTMLElement, required = false): HTMLElement {
  return h("label", { class: "field" },
    label + (required ? " *" : ""),
    h("span", { class: "error" }),
    input,
  );
}

function calcAge(ddmmyyyy: string): number | null {
  const parts = ddmmyyyy.split(".");
  if (parts.length !== 3) return null;
  const [dd, mm, yyyy] = parts.map(Number);
  if (!dd || !mm || !yyyy || yyyy < 1900) return null;
  const today = new Date();
  let age = today.getFullYear() - yyyy;
  if (today.getMonth() + 1 < mm || (today.getMonth() + 1 === mm && today.getDate() < dd)) age--;
  return age;
}

function buildForm(el: AppointmentForm): HTMLElement {
  const doctorId = el.getAttribute("doctor-id") ?? "";
  const serviceId = el.getAttribute("service-id") ?? "";
  const price = el.getAttribute("price") ?? "0";
  const startTime = el.getAttribute("start-time") ?? "";
  const durationMin = Number(el.getAttribute("duration-min") ?? "30");
  const ageMinAttr = el.getAttribute("age-min");
  const ageMaxAttr = el.getAttribute("age-max");
  const ageLimit: AgeLimit | null =
    ageMinAttr !== null && ageMaxAttr !== null
      ? { min: Number(ageMinAttr), max: Number(ageMaxAttr) }
      : null;

  const endTime = (() => {
    if (!startTime) return "";
    const ms = new Date(startTime.replace(" ", "T")).getTime() + durationMin * 60000;
    const d = new Date(ms);
    const pad = (n: number) => String(n).padStart(2, "0");
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
  })();

  const ageWarning = h("div", { class: "as-age-warning hidden" });

  const birthdayEl = h("input", {
    name: "birthday",
    type: "text",
    required: "",
    autocomplete: "bday",
    placeholder: "ДД.ММ.ГГГГ",
  }) as HTMLInputElement;

  birthdayEl.addEventListener("input", () => {
    let v = birthdayEl.value.replace(/\D/g, "").slice(0, 8);
    if (v.length > 4) v = v.slice(0, 2) + "." + v.slice(2, 4) + "." + v.slice(4);
    else if (v.length > 2) v = v.slice(0, 2) + "." + v.slice(2);
    birthdayEl.value = v;
    ageWarning.classList.add("hidden");
  });

  const form = h(
    "form",
    {
      class: "as-form",
      "data-action": "/api/medflex/appointment-specialist",
      "data-method": "post",
      "data-validator": "/api/validator/medflex/appointment-specialist",
      "data-messaging": "html",
      novalidate: "",
    },
    h("input", { type: "hidden", name: "doctor_id", value: doctorId }),
    h("input", { type: "hidden", name: "service_id", value: serviceId }),
    h("input", { type: "hidden", name: "price", value: price }),
    h("input", { type: "hidden", name: "start_time", value: startTime }),
    h("input", { type: "hidden", name: "end_time", value: endTime }),
    h("div", { class: "hidden message" }),
    ageWarning,
    h("h6", {}, "Введите личные данные"),
    h("div", { class: "as-form-row" },
      field("Фамилия", h("input", { name: "last_name", type: "text", required: "", autocomplete: "family-name", placeholder: "Иванов" }), true),
      field("Имя", h("input", { name: "first_name", type: "text", required: "", autocomplete: "given-name", placeholder: "Иван" }), true),
      field("Отчество", h("input", { name: "second_name", type: "text", required: "", autocomplete: "additional-name", placeholder: "Иванович" }), true),
    ),
    h("div", { class: "as-form-row" },
      field("Телефон", (() => {
        const phoneEl = h("input", { name: "mobile_phone", type: "tel", required: "", autocomplete: "tel", placeholder: "7XXXXXXXXXX" }) as HTMLInputElement;
        phoneEl.addEventListener("input", () => { phoneEl.value = phoneEl.value.replace(/\D/g, "").slice(0, 11); });
        return phoneEl;
      })(), true),
      field("Дата рождения", birthdayEl, true),
    ),
    field("Комментарий", h("textarea", { name: "comment", maxlength: 500, placeholder: "Пожелания, симптомы (необязательно)" })),
    h("label", { class: "as-consent" },
      h("input", { type: "checkbox", name: "consent", required: "", checked: "checked" }),
      h("span", {}, "Отсылая эту форму, я соглашаюсь на ", h("a", { href: "/personal-data-consent", target: "_blank" }, "обработку персональных данных"), " *"),
    ),
    h("button", { type: "submit", class: "ML float-right" }, "Записаться"),
  );

  if (ageLimit) {
    const { min, max } = ageLimit;
    form.addEventListener("submit", (e) => {
      const age = calcAge(birthdayEl.value);
      if (age === null || age < min || age > max) {
        e.preventDefault();
        e.stopImmediatePropagation();
        ageWarning.textContent = age === null
          ? "Пожалуйста, укажите дату рождения в формате ДД.ММ.ГГГГ."
          : `Приём ведётся для пациентов от ${min} до ${max} лет.`;
        ageWarning.classList.remove("hidden");
      }
    }, true); // capture phase — fires before external form utility
  }

  return form;
}

export class AppointmentForm extends HTMLElement {
  static observedAttributes = [
    "doctor-id",
    "service-id",
    "price",
    "start-time",
    "duration-min",
    "age-min",
    "age-max",
  ];

  connectedCallback(): void {
    this._render();
  }

  attributeChangedCallback(): void {
    if (this.isConnected) this._render();
  }

  private _render(): void {
    const doctorId = this.getAttribute("doctor-id");
    const serviceId = this.getAttribute("service-id");
    const startTime = this.getAttribute("start-time");
    if (!doctorId || !serviceId || !startTime) {
      this.replaceChildren();
      return;
    }
    this.replaceChildren(buildForm(this));
  }
}
