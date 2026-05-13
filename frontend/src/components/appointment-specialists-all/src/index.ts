import { AppointmentSpecialistsAll } from "./component";

if (!customElements.get("appointment-specialists-all")) {
  customElements.define("appointment-specialists-all", AppointmentSpecialistsAll);
}

export { AppointmentSpecialistsAll };
