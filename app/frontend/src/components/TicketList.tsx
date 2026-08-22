import type { Customer, Ticket } from "../types";
import { TicketCard } from "./TicketCard";
export function TicketList({
  tickets,
  customers,
  selectedId,
  onSelect,
}: {
  tickets: Ticket[];
  customers: Customer[];
  selectedId?: number;
  onSelect: (id: number) => void;
}) {
  if (!tickets.length)
    return <p className="empty">No tickets match this view.</p>;
  return (
    <div className="card-grid">
      {tickets.map((ticket) => (
        <TicketCard
          key={ticket.id}
          ticket={ticket}
          customer={customers.find(({ id }) => id === ticket.customerId)}
          selected={selectedId === ticket.id}
          onSelect={onSelect}
        />
      ))}
    </div>
  );
}
