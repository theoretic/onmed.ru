import type { Service } from "../types";

// Parse the `service-order` attribute: comma-separated Medflex speciality IDs.
export function parseServiceOrder(attr: string | null): string[] {
  return (attr ?? "")
    .split(",")
    .map((s) => s.trim())
    .filter(Boolean);
}

// Sort services by their position in `order`; services not listed keep their relative order and go last.
export function orderServices(services: Service[], order: string[]): Service[] {
  if (!order.length) return services;
  const rank = (s: Service) => {
    const i = order.indexOf(s.id);
    return i === -1 ? order.length : i;
  };
  return services
    .map((s, i) => ({ s, i }))
    .sort((a, b) => rank(a.s) - rank(b.s) || a.i - b.i)
    .map(({ s }) => s);
}
