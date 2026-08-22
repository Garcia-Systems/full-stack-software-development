import { Link } from "react-router-dom";
import type { Customer, Ticket } from "../types";
import { StatusBadge } from "./StatusBadge";

export function TicketCard({
  ticket,
  customer,
  selected,
  onSelect,
}: {
  ticket: Ticket;
  customer?: Customer;
  selected: boolean;
  onSelect: (id: number) => void;
}) {
  return (
    <article className={selected ? "card card--selected" : "card"}>
      <div>
        <StatusBadge status={ticket.status} />{" "}
        <span className={`priority priority--${ticket.priority}`}>
          {ticket.priority}
        </span>
      </div>
      <h3>
        <Link to={`/tickets/${ticket.id}`}>{ticket.subject}</Link>
      </h3>
      <p>{customer?.name ?? "Unknown customer"}</p>
      <button
        type="button"
        className="secondary"
        aria-pressed={selected}
        onClick={() => onSelect(ticket.id)}
      >
        {selected ? "Selected" : "Select ticket"}
      </button>
    </article>
  );
}
