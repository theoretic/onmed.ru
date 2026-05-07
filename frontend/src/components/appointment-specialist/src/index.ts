import { AppointmentSpecialist } from "./component";

if (!customElements.get("appointment-specialist")) {
  customElements.define("appointment-specialist", AppointmentSpecialist);
}

export { AppointmentSpecialist };
