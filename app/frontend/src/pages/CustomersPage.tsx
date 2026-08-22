import type { Customer } from "../types";
export function CustomersPage({ customers }: { customers: Customer[] }) {
  return (
    <section>
      <p className="eyebrow">Accounts</p>
      <h1>Customers</h1>
      <div className="card-grid">
        {customers.map((customer) => (
          <article className="card" key={customer.id}>
            <h2>{customer.name}</h2>
            <p>{customer.email ?? "No email on file"}</p>
            <span className="badge">
              {customer.isActive ? "active" : "inactive"}
            </span>
          </article>
        ))}
      </div>
    </section>
  );
}
