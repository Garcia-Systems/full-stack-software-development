export type TicketStatus = "open" | "in_progress" | "closed";
export type Priority = "low" | "normal" | "high" | "urgent";

export interface Customer {
  id: number;
  name: string;
  email?: string;
  isActive: boolean;
}
export interface Ticket {
  id: number;
  customerId: number;
  subject: string;
  description?: string;
  status: TicketStatus;
  priority: Priority;
}
export interface DraftTicket {
  customerId: number;
  subject: string;
  priority: Priority;
}
export type LoadState<T> =
  | { status: "idle" | "loading" }
  | { status: "success"; data: T }
  | { status: "empty" }
  | { status: "error"; message: string };
