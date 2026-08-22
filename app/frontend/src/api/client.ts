import type { Customer, DraftTicket, Ticket } from "../types";

export type ApiProblemType =
  | "validation"
  | "unauthenticated"
  | "forbidden"
  | "not_found"
  | "conflict"
  | "unexpected"
  | "network";
export class ApiError extends Error {
  constructor(
    public type: ApiProblemType,
    message: string,
    public status?: number,
    public fields?: Record<string, string[]>,
    public requestId?: string,
  ) {
    super(message);
  }
}
type Envelope<T> = { data: T; requestId: string };
const base = import.meta.env.VITE_API_URL ?? "/api/v1";
async function request<T>(path: string, init: RequestInit = {}): Promise<T> {
  let response: Response;
  try {
    response = await fetch(`${base}${path}`, {
      ...init,
      credentials: "include",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        "X-Request-ID": crypto.randomUUID(),
        ...init.headers,
      },
    });
  } catch {
    throw new ApiError("network", "The request could not reach the server.");
  }
  const body = response.status === 204 ? undefined : await response.json();
  if (!response.ok) {
    const map: Record<number, ApiProblemType> = {
      401: "unauthenticated",
      403: "forbidden",
      404: "not_found",
      409: "conflict",
      422: "validation",
    };
    throw new ApiError(
      map[response.status] ?? "unexpected",
      body?.error?.message ??
        body?.message ??
        "The server could not complete the request.",
      response.status,
      body?.error?.fields,
      body?.request_id,
    );
  }
  return body as T;
}
export const sessionApi = {
  login: (email: string, password: string) =>
    request<{ user: { name: string } }>("/session", {
      method: "POST",
      body: JSON.stringify({ email, password }),
    }),
  logout: () => request<void>("/session", { method: "DELETE" }),
  current: () => request<{ user: { name: string } }>("/session"),
};
export const customerApi = {
  async list(): Promise<Customer[]> {
    return (await request<Envelope<Customer[]>>("/customers?organization_id=1"))
      .data;
  },
};
export const apiTicketRepository = {
  async list({
    signal,
    lab,
  }: { signal?: AbortSignal; lab?: string } = {}): Promise<Ticket[]> {
    return (
      await request<Envelope<Ticket[]>>("/tickets?organization_id=1", {
        signal,
        headers: lab ? { "X-RelayDesk-Lab": lab } : {},
      })
    ).data;
  },
  async create(draft: DraftTicket): Promise<Ticket> {
    return (
      await request<Envelope<Ticket>>("/tickets", {
        method: "POST",
        body: JSON.stringify({ organization_id: 1, ...draft }),
        headers: { "Idempotency-Key": crypto.randomUUID() },
      })
    ).data;
  },
};
