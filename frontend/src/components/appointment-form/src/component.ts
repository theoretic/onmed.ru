import { h } from "../../../_shared/lib/dom";
import type { AgeLimit } from "../../../_shared/lib/types";

// Returns dd.mm.yy date and "HH:MM\u00a0\u2013\u00a0HH:MM" time range from the raw
// "YYYY-MM-DD HH:MM" strings already computed inside buildForm().
function formatCouponDateTime(startTimeStr: string, endTimeStr: string): { date: string; timeRange: string } {
  const [datePart, startHHMM = "00:00"] = startTimeStr.split(" ");
  const [y, mo, d] = datePart.split("-");
  const date = `${d}.${mo}.${y.slice(-2)}`;
  const endHHMM = endTimeStr.split(" ")[1] ?? "00:00";
  return { date, timeRange: `${startHHMM}\u00a0\u2013\u00a0${endHHMM}` };
}

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

function mountCancelButton(claimId: string, wrapper: HTMLElement, couponDiv: HTMLElement, messageDiv: HTMLElement): void {
  const btn = h("button", { type: "button", class: "as-cancel-btn alert" }, "\u041e\u0442\u043c\u0435\u043d\u0438\u0442\u044c \u0437\u0430\u043f\u0438\u0441\u044c") as HTMLButtonElement;
  const errEl = h("span", { class: "as-cancel-err error hidden" });
  const showError = (text: string) => {
    errEl.textContent = text;
    errEl.classList.remove("hidden");
    btn.disabled = false;
  };
  btn.addEventListener("click", async () => {
    btn.disabled = true;
    errEl.classList.add("hidden");
    try {
      const body = new URLSearchParams({ claim_id: claimId });
      const res = await fetch("/api/medflex/appointment/cancel/", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: body.toString(),
      });
      const json = await res.json() as { success?: string; error?: string };
      if (json.success) {
        messageDiv.className = "message warning";
        messageDiv.textContent = json.success;
        couponDiv.classList.add("hidden");
        wrapper.classList.add("hidden");
      } else {
        showError(json.error ?? "\u041d\u0435 \u0443\u0434\u0430\u043b\u043e\u0441\u044c \u043e\u0442\u043c\u0435\u043d\u0438\u0442\u044c \u0437\u0430\u043f\u0438\u0441\u044c.");
      }
    } catch {
      showError("\u041d\u0435 \u0443\u0434\u0430\u043b\u043e\u0441\u044c \u043e\u0442\u043c\u0435\u043d\u0438\u0442\u044c \u0437\u0430\u043f\u0438\u0441\u044c. \u041f\u043e\u0436\u0430\u043b\u0443\u0439\u0441\u0442\u0430, \u043f\u043e\u0437\u0432\u043e\u043d\u0438\u0442\u0435 \u043d\u0430\u043c.");
    }
  });
  wrapper.appendChild(btn);
  wrapper.appendChild(errEl);
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
    // Parse as local time — Safari treats new Date("YYYY-MM-DDTHH:MM") as UTC,
    // shifting the time by the UTC offset and producing a wrong end_time for Medflex.
    const [datePart, timePart = "00:00"] = startTime.split(" ");
    const [y, mo, d] = datePart.split("-").map(Number);
    const [h = 0, min = 0] = timePart.split(":").map(Number);
    const ms = new Date(y, mo - 1, d, h, min).getTime() + durationMin * 60000;
    const r = new Date(ms);
    const pad = (n: number) => String(n).padStart(2, "0");
    return `${r.getFullYear()}-${pad(r.getMonth() + 1)}-${pad(r.getDate())} ${pad(r.getHours())}:${pad(r.getMinutes())}`;
  })();

  const ageWarning = h("div", { class: "as-age-warning hidden" });

  // Plain text input with DD.MM.YYYY format. Backend
  // (api/medflex/appointment/make.php) accepts DD.MM.YYYY and ISO and
  // converts to ISO for Medflex. We still normalize client-side so common
  // autofill formats (ISO, US slashes) become DD.MM.YYYY before validation.
  const birthdayEl = h("input", {
    name: "birthday",
    type: "text",
    inputmode: "numeric",
    required: "",
    placeholder: "ДД.ММ.ГГГГ",
    pattern: "\\d{2}\\.\\d{2}\\.\\d{4}",
    maxlength: "10",
  }) as HTMLInputElement;

  const normalizeBirthday = (raw: string): string => {
    const v = raw.trim();
    // ISO: 1990-05-15 (Safari Keychain / Contacts autofill)
    let m = v.match(/^(\d{4})[-/.](\d{1,2})[-/.](\d{1,2})$/);
    if (m) {
      const [, y, mo, d] = m;
      return `${d.padStart(2, "0")}.${mo.padStart(2, "0")}.${y}`;
    }
    // DD.MM.YYYY / DD/MM/YYYY / DD-MM-YYYY
    m = v.match(/^(\d{1,2})[./-](\d{1,2})[./-](\d{4})$/);
    if (m) {
      const [, d, mo, y] = m;
      return `${d.padStart(2, "0")}.${mo.padStart(2, "0")}.${y}`;
    }
    // Fallback: digits-only → pack as DD.MM.YYYY
    const digits = v.replace(/\D/g, "").slice(0, 8);
    if (digits.length > 4) return digits.slice(0, 2) + "." + digits.slice(2, 4) + "." + digits.slice(4);
    if (digits.length > 2) return digits.slice(0, 2) + "." + digits.slice(2);
    return digits;
  };
  const onBirthdayChange = () => {
    birthdayEl.value = normalizeBirthday(birthdayEl.value);
    ageWarning.classList.add("hidden");
  };
  birthdayEl.addEventListener("input", onBirthdayChange);
  birthdayEl.addEventListener("change", onBirthdayChange);
  birthdayEl.addEventListener("blur", onBirthdayChange);

  // While the user is still typing an incomplete date (e.g. "15", "15.0"),
  // stop the input event from bubbling up to the document-level form
  // validator. Otherwise every keystroke would fire a server roundtrip that
  // returns "не dd.mm.yyyy", marking the field invalid mid-typing — visible
  // as flicker. Full-form validate() on submit still catches genuinely
  // empty/invalid values.
  birthdayEl.addEventListener("input", (e) => {
    if (!/^\d{2}\.\d{2}\.\d{4}$/.test(birthdayEl.value)) {
      e.stopPropagation();
    }
  });

  const doctorName = el.getAttribute("doctor-name") ?? "";
  const doctorSpeciality = el.getAttribute("doctor-speciality") ?? "";
  const { date: couponDate, timeRange: couponTimeRange } = formatCouponDateTime(startTime, endTime);

  const couponDiv = h("div", { class: "as-coupon centered hidden padded" },
    h("h5", {}, "\u0422\u0430\u043b\u043e\u043d \u043d\u0430 \u043f\u0440\u0438\u0451\u043c"),
    ...(doctorName ? [
      h("div", { class: "half-padded as-coupon-row" },
        h("div", { class: "as-coupon-label comment" }, "\u0412\u0440\u0430\u0447"),
        h("span", { class: "as-coupon-value" }, doctorName),
      ),
    ] : []),
    ...(doctorSpeciality ? [
      h("div", { class: "half-padded as-coupon-row" },
        h("div", { class: "as-coupon-label comment" }, "\u0421\u043f\u0435\u0446\u0438\u0430\u043b\u0438\u0437\u0430\u0446\u0438\u044f"),
        h("span", { class: "as-coupon-value" }, doctorSpeciality),
      ),
    ] : []),
    h("div", { class: "half-padded as-coupon-row" },
      h("div", { class: "as-coupon-label comment" }, "\u0414\u0430\u0442\u0430"),
      h("span", { class: "as-coupon-value" }, couponDate),
    ),
    h("div", { class: "half-padded as-coupon-row" },
      h("div", { class: "as-coupon-label comment" }, "\u0412\u0440\u0435\u043c\u044f"),
      h("span", { class: "as-coupon-value" }, couponTimeRange),
    ),
    h("button", { type: "button", class: "as-coupon-print" }, "\u0420\u0430\u0441\u043f\u0435\u0447\u0430\u0442\u0430\u0442\u044c \u0442\u0430\u043b\u043e\u043d"),
  );

  const cancelWrapper = h("div", { class: "as-cancel-wrapper padded centered hidden" });

  // Print only the coupon: clone it to <body> so CSS can hide all other direct
  // body children with display:none — the only reliable way to avoid whitespace
  // from invisible ancestor elements that still occupy space in print layout.
  const printBtn = couponDiv.querySelector(".as-coupon-print") as HTMLButtonElement;
  printBtn.addEventListener("click", () => {
    const clone = couponDiv.cloneNode(true) as HTMLElement;
    clone.classList.add("as-coupon-print-portal");
    clone.classList.remove("hidden");
    document.body.appendChild(clone);
    document.body.classList.add("as-printing-coupon");

    const cleanup = () => {
      document.body.classList.remove("as-printing-coupon");
      document.body.removeChild(clone);
      window.removeEventListener("afterprint", cleanup);
    };
    window.addEventListener("afterprint", cleanup);
    window.print();
  });

  const messageDiv = h("div", { class: "hidden message" });

  // Wrap all interactive form content in a dedicated div so it can be hidden
  // after a successful submission while the success message remains visible.
  const formBody = h("div", { class: "as-form-body" },
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
        // Safari autofill (`autocomplete="tel"`) yields formatted values like
        // "+7 (495) 123-45-67" without firing `input`. Normalize on every event
        // that can supply a value.
        const normalizePhone = () => { phoneEl.value = phoneEl.value.replace(/\D/g, "").slice(0, 11); };
        phoneEl.addEventListener("input", normalizePhone);
        phoneEl.addEventListener("change", normalizePhone);
        phoneEl.addEventListener("blur", normalizePhone);
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

  const form = h(
    "form",
    {
      class: "as-form",
      // Trailing slashes match the convention used by all other Medflex
      // endpoints (/schedule/, /doctor/, /speciality/). Apache's rewrite
      // accepts both forms, but keeping the convention consistent avoids
      // any chance of a mod_dir 301 dropping the POST body on iOS Safari.
      "data-action": "/api/medflex/appointment/make/",
      "data-method": "post",
      "data-validator": "/api/validator/medflex/appointment/make/",
      "data-messaging": "html",
      novalidate: "",
    },
    h("input", { type: "hidden", name: "doctor_id", value: doctorId }),
    h("input", { type: "hidden", name: "service_id", value: serviceId }),
    h("input", { type: "hidden", name: "price", value: price }),
    h("input", { type: "hidden", name: "start_time", value: startTime }),
    h("input", { type: "hidden", name: "end_time", value: endTime }),
    messageDiv,
    formBody,
    cancelWrapper,
    couponDiv,
  );

  // Hide the fields and reveal the coupon when FormHelper marks the message
  // div as success (removes "hidden", adds "success" to its class list).
  const observer = new MutationObserver(() => {
    if (messageDiv.classList.contains("success")) {
      formBody.classList.add("hidden");
      couponDiv.classList.remove("hidden");
      observer.disconnect();

      // Extract claim_id embedded by make.php as a hidden <span data-id="...">.
      // If present, mount the cancel button in the pre-created wrapper.
      const claimSpan = messageDiv.querySelector<HTMLElement>(".as-claim-id[data-id]");
      const claimId = claimSpan?.dataset.id ?? "";
      if (claimId) {
        mountCancelButton(claimId, cancelWrapper, couponDiv, messageDiv);
        cancelWrapper.classList.remove("hidden");
      }
    }
  });
  observer.observe(messageDiv, { attributes: true, attributeFilter: ["class"] });

  // Final safety net: re-normalize on submit (capture phase runs before the
  // external form utility serializes the FormData). This catches Safari
  // autofill paths that bypass `input`/`change`/`blur`.
  const normalizeAll = () => {
    birthdayEl.value = normalizeBirthday(birthdayEl.value);
    const phone = form.querySelector<HTMLInputElement>('input[name="mobile_phone"]');
    if (phone) phone.value = phone.value.replace(/\D/g, "").slice(0, 11);
  };
  form.addEventListener("submit", normalizeAll, true);

  // iOS Safari (WKWebView) can fire submit before the previously focused
  // input's `blur` handler runs when the user taps the submit button. Run
  // normalization on pointerdown/touchstart on the button so values are
  // already canonical by the time the form's submit event fires.
  const submitBtn = form.querySelector<HTMLButtonElement>('button[type="submit"]');
  if (submitBtn) {
    submitBtn.addEventListener("pointerdown", normalizeAll);
    submitBtn.addEventListener("touchstart", normalizeAll, { passive: true });
  }

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
    "doctor-name",
    "doctor-speciality",
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
