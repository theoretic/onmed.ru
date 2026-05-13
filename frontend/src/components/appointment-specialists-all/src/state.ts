import type { AppState } from "./types";

type Listener = (s: AppState) => void;

export class Store {
  private _state: AppState;
  private _subs = new Set<Listener>();

  constructor(initial: AppState) {
    this._state = initial;
  }

  get state(): AppState {
    return this._state;
  }

  set(patch: Partial<AppState>): void {
    this._state = { ...this._state, ...patch };
    for (const l of this._subs) l(this._state);
  }

  subscribe(l: Listener): () => void {
    this._subs.add(l);
    return () => this._subs.delete(l);
  }
}

export function createInitial(apiBase: string): AppState {
  const now = new Date();
  return {
    phase: "idle",
    apiBase,
    visibleMonth: new Date(now.getFullYear(), now.getMonth(), 1),
  };
}
