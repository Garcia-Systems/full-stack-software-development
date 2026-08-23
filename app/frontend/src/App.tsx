import { NavLink, Route, Routes } from "react-router-dom";
import { customers } from "./data/fixtures";
import { apiTicketRepository, sessionApi } from "./api/client";
import { useEffect, useState } from "react";
import { useTickets } from "./hooks/useTickets";
import { CustomersPage } from "./pages/CustomersPage";
import { DashboardPage } from "./pages/DashboardPage";
import { NewTicketPage } from "./pages/NewTicketPage";
import { TicketDetailPage } from "./pages/TicketDetailPage";
import { TicketsPage } from "./pages/TicketsPage";

export function App() {
  const { state, load, create } = useTickets(apiTicketRepository);
  const [identity, setIdentity] = useState<{ name: string; role: string }>();
  useEffect(() => {
    sessionApi
      .current()
      .then(setIdentity)
      .catch(() => undefined);
  }, []);
  async function login() {
    const result = await sessionApi.login("alice@relaydesk.test", "password");
    setIdentity({ name: result.user.name, role: result.user.role });
    load();
  }
  async function logout() {
    await sessionApi.logout();
    setIdentity(undefined);
    load();
  }
  const tickets = state.status === "success" ? state.data : [];
  return (
    <div className="shell">
      <header>
        <a className="brand" href="/">
          RelayDesk <small>Part VI</small>
        </a>
        <nav aria-label="Primary">
          <NavLink to="/">Dashboard</NavLink>
          <NavLink to="/tickets">Tickets</NavLink>
          <NavLink to="/customers">Customers</NavLink>
          {identity?.role !== "viewer" && (
            <NavLink to="/tickets/new">New ticket</NavLink>
          )}
        </nav>
        {identity ? (
          <button onClick={logout}>Log out {identity.name}</button>
        ) : (
          <button onClick={login}>Log in as seeded Alice</button>
        )}
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
        React → Laravel → MySQL → queue worker → controlled dependency
      </footer>
    </div>
  );
}
