import { useMemo, useState } from "react";
import type { Customer, Ticket } from "../types";
import { TicketList } from "../components/TicketList";
export function TicketsPage({
  tickets,
  customers,
}: {
  tickets: Ticket[];
  customers: Customer[];
}) {
  const [query, setQuery] = useState("");
  const [selectedId, setSelectedId] = useState<number>();
  const visible = useMemo(
    () =>
      tickets.filter(({ subject }) =>
        subject.toLowerCase().includes(query.toLowerCase()),
      ),
    [tickets, query],
  );
  return (
    <section>
      <p className="eyebrow">Queue</p>
      <h1>Support tickets</h1>
      <label className="filter">
        Filter tickets
        <input
          value={query}
          onChange={(event) => setQuery(event.target.value)}
          placeholder="Try export"
        />
      </label>
      <p role="status">
        Showing {visible.length} of {tickets.length}
      </p>
      <TicketList
        tickets={visible}
        customers={customers}
        selectedId={selectedId}
        onSelect={setSelectedId}
      />
    </section>
  );
}
