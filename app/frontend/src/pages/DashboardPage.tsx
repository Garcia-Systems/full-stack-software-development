import { Link } from "react-router-dom";
import type { Ticket } from "../types";
export function DashboardPage({ tickets }: { tickets: Ticket[] }) {
  const open = tickets.filter(({ status }) => status !== "closed").length;
  return (
    <section>
      <p className="eyebrow">Operational summary</p>
      <h1>Good morning, support team</h1>
      <div className="metrics">
        <article>
          <strong>{open}</strong>
          <span>active tickets</span>
        </article>
        <article>
          <strong>
            {
              tickets.filter(
                ({ priority }) => priority === "urgent" || priority === "high",
              ).length
            }
          </strong>
          <span>high attention</span>
        </article>
      </div>
      <Link className="button" to="/tickets">
        Review the queue
      </Link>
    </section>
  );
}
