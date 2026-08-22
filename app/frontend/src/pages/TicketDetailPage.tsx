import { Link, useParams } from "react-router-dom";
import type { Customer, Ticket } from "../types";
import { StatusBadge } from "../components/StatusBadge";
export function TicketDetailPage({
  tickets,
  customers,
}: {
  tickets: Ticket[];
  customers: Customer[];
}) {
  const { ticketId } = useParams();
  const ticket = tickets.find(({ id }) => id === Number(ticketId));
  if (!ticket)
    return (
      <section>
        <h1>Ticket not found</h1>
        <p>
          The URL parameter <code>{ticketId}</code> does not identify a local
          fixture.
        </p>
        <Link to="/tickets">Back to tickets</Link>
      </section>
    );
  const customer = customers.find(({ id }) => id === ticket.customerId);
  return (
    <section>
      <p className="eyebrow">Ticket #{ticket.id}</p>
      <h1>{ticket.subject}</h1>
      <StatusBadge status={ticket.status} />
      <dl>
        <dt>Customer</dt>
        <dd>{customer?.name ?? "Unknown customer"}</dd>
        <dt>Priority</dt>
        <dd>{ticket.priority}</dd>
        <dt>Description</dt>
        <dd>{ticket.description ?? "No description supplied."}</dd>
      </dl>
      <Link to="/tickets">Back to queue</Link>
    </section>
  );
}
