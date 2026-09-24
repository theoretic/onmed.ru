import { describe, expect, it } from "vitest";
import type { Service } from "../src/types";
import { orderServices, parseServiceOrder } from "../src/lib/orderServices";

const svc = (id: string): Service => ({ id, name: id, price: 0, durationMin: 30 });
const ids = (list: Service[]) => list.map((s) => s.id);

describe("parseServiceOrder", () => {
  it("splits and trims, ignoring blanks", () => {
    expect(parseServiceOrder(" 12, 5,,31 ")).toEqual(["12", "5", "31"]);
  });
  it("returns empty for missing attribute", () => {
    expect(parseServiceOrder(null)).toEqual([]);
  });
});

describe("orderServices", () => {
  const services = [svc("1"), svc("2"), svc("3"), svc("4")];

  it("keeps API order when no order given", () => {
    expect(ids(orderServices(services, []))).toEqual(["1", "2", "3", "4"]);
  });
  it("sorts listed services first, unlisted keep relative order", () => {
    expect(ids(orderServices(services, ["3", "1"]))).toEqual(["3", "1", "2", "4"]);
  });
  it("ignores order IDs the doctor has no service for", () => {
    expect(ids(orderServices(services, ["99", "4"]))).toEqual(["4", "1", "2", "3"]);
  });
});
