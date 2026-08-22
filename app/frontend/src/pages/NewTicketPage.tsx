import type { Customer, DraftTicket } from "../types";
import { TicketForm } from "../components/TicketForm";
export function NewTicketPage({
  customers,
  create,
}: {
  customers: Customer[];
  create: (draft: DraftTicket) => Promise<void>;
}) {
  return (
    <section>
      <p className="eyebrow">Authenticated operation</p>
      <h1>Create a ticket</h1>
      <p>
        The typed API client sends this representation to Laravel and renders
        the persisted response.
      </p>
      <TicketForm customers={customers} onSubmit={create} />
    </section>
  );
}
