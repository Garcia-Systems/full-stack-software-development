import type { TicketStatus } from "../types";
export function StatusBadge({ status }: { status: TicketStatus }) {
  return (
    <span className={`badge badge--${status}`}>{status.replace("_", " ")}</span>
  );
}
