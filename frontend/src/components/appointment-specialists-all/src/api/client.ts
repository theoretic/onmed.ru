// Fetch wrapper. All calls go through proxy that injects API key server-side.
// No auth header here.

export interface RequestOpts {
  method?: string;
  body?: unknown;
  query?: Record<string, string | number | undefined>;
  signal?: AbortSignal;
}

export class ApiError extends Error {
  constructor(
    msg: string,
    public status: number,
    public payload?: unknown,
  ) {
    super(msg);
  }
}

export async function request<T>(base: string, path: string, opts: RequestOpts = {}): Promise<T> {
  if (!base) throw new ApiError("API base URL not configured", 0);
  const url = new URL(path.replace(/^\//, ""), base.endsWith("/") ? base : `${base}/`);
  if (opts.query) {
    for (const k in opts.query) {
      const v = opts.query[k];
      if (v !== undefined) url.searchParams.set(k, String(v));
    }
  }
  const headers: Record<string, string> = { accept: "application/json" };
  let body: BodyInit | undefined;
  if (opts.body !== undefined) {
    body = JSON.stringify(opts.body);
    headers["content-type"] = "application/json";
  }
  const res = await fetch(url.toString(), {
    method: opts.method || "GET",
    headers,
    body,
    signal: opts.signal,
  });
  let payload: unknown;
  const ct = res.headers.get("content-type") || "";
  if (ct.includes("application/json")) {
    try {
      payload = await res.json();
    } catch {
      payload = undefined;
    }
  } else {
    try {
      payload = await res.text();
    } catch {
      payload = undefined;
    }
  }
  if (!res.ok) {
    throw new ApiError(`HTTP ${res.status}`, res.status, payload);
  }
  return payload as T;
}
