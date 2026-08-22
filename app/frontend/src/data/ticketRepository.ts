import { tickets as initialTickets } from "./fixtures";
import type { DraftTicket, Ticket } from "../types";

export interface TicketRepository {
  list(options?: {
    delay?: number;
    fail?: boolean;
    signal?: AbortSignal;
  }): Promise<Ticket[]>;
  create(draft: DraftTicket): Promise<Ticket>;
}

let records = initialTickets.map((ticket) => ({ ...ticket }));

function delay(milliseconds: number, signal?: AbortSignal): Promise<void> {
  return new Promise((resolve, reject) => {
    const timer = window.setTimeout(resolve, milliseconds);
    signal?.addEventListener(
      "abort",
      () => {
        window.clearTimeout(timer);
        reject(new DOMException("Request cancelled", "AbortError"));
      },
      { once: true },
    );
  });
}

export const fixtureTicketRepository: TicketRepository = {
  async list({ delay: wait = 300, fail = false, signal } = {}) {
    await delay(wait, signal);
    if (fail) throw new Error("The controlled fixture failed to load.");
    return records.map((ticket) => ({ ...ticket }));
  },
  async create(draft) {
    await delay(200);
    const ticket: Ticket = {
      id: Math.max(0, ...records.map(({ id }) => id)) + 1,
      ...draft,
      status: "open",
    };
    records = [...records, ticket];
    return { ...ticket };
  },
};
