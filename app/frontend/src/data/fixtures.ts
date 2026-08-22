import type { Customer, Ticket } from "../types";

export const customers: Customer[] = [
  {
    id: 1,
    name: "Northwind Studio",
    email: "hello@northwind.test",
    isActive: true,
  },
  {
    id: 2,
    name: "Paper Street Design",
    email: "ops@paperstreet.test",
    isActive: false,
  },
  { id: 3, name: "Acme Field Services", isActive: true },
];

export const tickets: Ticket[] = [
  {
    id: 1,
    customerId: 1,
    subject: "Cannot export the weekly report",
    description: "CSV export remains pending.",
    status: "open",
    priority: "high",
  },
  {
    id: 2,
    customerId: 1,
    subject: "Confirm migration window",
    status: "in_progress",
    priority: "normal",
  },
  {
    id: 3,
    customerId: 3,
    subject: "Update project contact",
    status: "closed",
    priority: "low",
  },
];
