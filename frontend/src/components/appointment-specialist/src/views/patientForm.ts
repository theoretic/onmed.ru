import { h } from "../lib/dom";

export interface PatientFormOpts {
  doctorId: string;
  serviceId: string;
  servicePrice: number;
  serviceDurationMin: number;
  startISO: string;
}

function field(label: string, input: HTMLElement, required = false): HTMLElement {
  return h("label", { class: "field" },
    label + (required ? " *" : ""),
    h("span", { class: "error" }),
    input,
  );
}

// Plain <form> emitted in light DOM. External page-level utility picks up
// `data-action`, `data-method`, `data-validator` and submits/validates.
// Widget itself performs no validation and no POST.
export function renderPatientForm(opts: PatientFormOpts): HTMLElement {
  return h(
    "form",
    {
      class: "as-form",
      "data-action": "/api/medflex/appointment-specialist",
      "data-method": "post",
      "data-validator": "/api/validator/medflex/appointment-specialist",
      "data-messaging": "html",
      novalidate: "",
    },
    h("input", { type: "hidden", name: "doctor_id", value: opts.doctorId }),
    h("input", { type: "hidden", name: "service_id", value: opts.serviceId }),
    h("input", { type: "hidden", name: "price", value: String(opts.servicePrice) }),
    h("input", { type: "hidden", name: "start_time", value: opts.startISO }),
    h("input", { type: "hidden", name: "end_time", value: (() => {
      const ms = new Date(opts.startISO.replace(" ", "T")).getTime() + opts.serviceDurationMin * 60000;
      const d = new Date(ms);
      const pad = (n: number) => String(n).padStart(2, "0");
      return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
    })() }),
    h("div", { class: "hidden message" }),
    h("h6", {}, "Введите личные данные"),
    h("div", { class: "as-form-row" },
      field("Фамилия", h("input", { name: "last_name", type: "text", required: "", autocomplete: "family-name", placeholder: "Иванов" }), true),
      field("Имя", h("input", { name: "first_name", type: "text", required: "", autocomplete: "given-name", placeholder: "Иван" }), true),
      field("Отчество", h("input", { name: "second_name", type: "text", required: "", autocomplete: "additional-name", placeholder: "Иванович" }), true),
    ),
    h("div", { class: "as-form-row" },
      field("Телефон", (() => {
        const el = h("input", { name: "mobile_phone", type: "tel", required: "", autocomplete: "tel", placeholder: "7XXXXXXXXXX" }) as HTMLInputElement;
        el.addEventListener("input", () => { el.value = el.value.replace(/\D/g, "").slice(0, 11); });
        return el;
      })(), true),
      field("Дата рождения", (() => {
        const el = h("input", { name: "birthday", type: "text", required: "", autocomplete: "bday", placeholder: "ДД.ММ.ГГГГ" }) as HTMLInputElement;
        el.addEventListener("input", () => {
          let v = el.value.replace(/\D/g, "").slice(0, 8);
          if (v.length > 4) v = v.slice(0, 2) + "." + v.slice(2, 4) + "." + v.slice(4);
          else if (v.length > 2) v = v.slice(0, 2) + "." + v.slice(2);
          el.value = v;
        });
        return el;
      })(), true),
    ),
    field("Комментарий", h("textarea", { name: "comment", maxlength: 500, placeholder: "Пожелания, симптомы (необязательно)" })),
    h("label", { class: "as-consent" },
      h("input", { type: "checkbox", name: "consent", required: "", checked: "checked" }),
      h("span", {}, "Отсылая эту форму, я соглашаюсь на ", h("a", { href: "/personal-data-consent", target: "_blank" }, "обработку персональных данных"), " *"),
    ),
    h("button", { type: "submit", class: "ML" }, "Записаться"),
  );
}
