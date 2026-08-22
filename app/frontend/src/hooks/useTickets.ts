import { useCallback, useEffect, useState } from "react";
import type { DraftTicket, LoadState, Ticket } from "../types";
import type { TicketRepository } from "../data/ticketRepository";

export function useTickets(repository: TicketRepository) {
  const [state, setState] = useState<LoadState<Ticket[]>>({ status: "idle" });
  const load = useCallback(
    (fail = false) => {
      const controller = new AbortController();
      setState({ status: "loading" });
      repository
        .list({ fail, signal: controller.signal })
        .then((data) =>
          setState(
            data.length ? { status: "success", data } : { status: "empty" },
          ),
        )
        .catch((error: unknown) => {
          if (error instanceof DOMException && error.name === "AbortError")
            return;
          setState({
            status: "error",
            message: error instanceof Error ? error.message : "Unknown error",
          });
        });
      return () => controller.abort();
    },
    [repository],
  );
  useEffect(() => load(), [load]);
  async function create(draft: DraftTicket) {
    const created = await repository.create(draft);
    setState((current) =>
      current.status === "success"
        ? { status: "success", data: [...current.data, created] }
        : { status: "success", data: [created] },
    );
  }
  return { state, load, create };
}
