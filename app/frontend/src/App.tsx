import { NavLink, Route, Routes } from "react-router-dom";
import { customers } from "./data/fixtures";
import { fixtureTicketRepository } from "./data/ticketRepository";
import { useTickets } from "./hooks/useTickets";
import { CustomersPage } from "./pages/CustomersPage";
import { DashboardPage } from "./pages/DashboardPage";
import { NewTicketPage } from "./pages/NewTicketPage";
import { TicketDetailPage } from "./pages/TicketDetailPage";
import { TicketsPage } from "./pages/TicketsPage";

export function App() {
  const { state, load, create } = useTickets(fixtureTicketRepository);
  const tickets = state.status === "success" ? state.data : [];
  return (
    <div className="shell">
      <header>
        <a className="brand" href="/">
          RelayDesk <small>Part IV</small>
        </a>
        <nav aria-label="Primary">
          <NavLink to="/">Dashboard</NavLink>
          <NavLink to="/tickets">Tickets</NavLink>
          <NavLink to="/customers">Customers</NavLink>
          <NavLink to="/tickets/new">New ticket</NavLink>
        </nav>
      </header>
      <main>
        {state.status === "loading" && (
          <p role="status" className="notice">
            Loading the controlled fixture…
          </p>
        )}
        {state.status === "error" && (
          <div role="alert" className="notice notice--error">
            <p>{state.message}</p>
            <button onClick={() => load()}>Retry</button>
          </div>
        )}
        {state.status === "empty" && (
          <p className="notice">The fixture contains no tickets.</p>
        )}
        {state.status === "success" && (
          <Routes>
            <Route path="/" element={<DashboardPage tickets={tickets} />} />
            <Route
              path="/tickets"
              element={<TicketsPage tickets={tickets} customers={customers} />}
            />
            <Route
              path="/tickets/new"
              element={<NewTicketPage customers={customers} create={create} />}
            />
            <Route
              path="/tickets/:ticketId"
              element={
                <TicketDetailPage tickets={tickets} customers={customers} />
              }
            />
            <Route
              path="/customers"
              element={<CustomersPage customers={customers} />}
            />
            <Route
              path="*"
              element={
                <section>
                  <h1>Page not found</h1>
                  <p>
                    Inspect the address bar: the client route did not match.
                  </p>
                </section>
              }
            />
          </Routes>
        )}
      </main>
      <footer>
        Fixture-backed learning client · no authentication or production API yet
      </footer>
    </div>
  );
}
