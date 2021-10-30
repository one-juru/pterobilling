export interface Action<T extends string> {
  type: T
}

export interface ActionWithPayload<T extends string, P extends unknown> extends Action<T> {
  payload: P
}

export function typedAction<T extends string>(type: T): { type: T }
export function typedAction<T extends string, P extends unknown>(
  type: T,
  payload: P
): { type: T; payload: P }
export function typedAction(
  type: string,
  payload?: unknown
): Action<string> | ActionWithPayload<string, unknown> {
  return { type, payload }
}
