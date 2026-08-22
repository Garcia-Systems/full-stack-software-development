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
      <p className="eyebrow">Local draft</p>
      <h1>Create a ticket</h1>
      <p>
        This Part IV adapter saves to deterministic in-memory fixtures—not
        Laravel. Part V will replace that boundary.
      </p>
      <TicketForm customers={customers} onSubmit={create} />
    </section>
  );
}
