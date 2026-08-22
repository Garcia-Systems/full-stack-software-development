import { useState, type FormEvent } from "react";
import type { Customer, DraftTicket, Priority } from "../types";

type Errors = Partial<Record<"customerId" | "subject", string>>;
export function TicketForm({
  customers,
  onSubmit,
}: {
  customers: Customer[];
  onSubmit: (draft: DraftTicket) => Promise<void>;
}) {
  const [subject, setSubject] = useState("");
  const [customerId, setCustomerId] = useState("");
  const [priority, setPriority] = useState<Priority>("normal");
  const [errors, setErrors] = useState<Errors>({});
  const [pending, setPending] = useState(false);
  const [message, setMessage] = useState("");
  async function submit(event: FormEvent) {
    event.preventDefault();
    const next: Errors = {};
    if (subject.trim().length < 5) next.subject = "Use at least 5 characters.";
    if (!customerId) next.customerId = "Choose a customer.";
    setErrors(next);
    if (Object.keys(next).length) {
      setMessage("Fix the highlighted fields.");
      return;
    }
    setPending(true);
    setMessage("Saving locally…");
    try {
      await onSubmit({
        subject: subject.trim(),
        customerId: Number(customerId),
        priority,
      });
      setSubject("");
      setMessage("Ticket added to this browser session.");
    } catch {
      setMessage("The ticket could not be saved. Try again.");
    } finally {
      setPending(false);
    }
  }
  return (
    <form onSubmit={submit} noValidate>
      <label>
        Customer
        <select
          value={customerId}
          onChange={(event) => setCustomerId(event.target.value)}
          aria-invalid={Boolean(errors.customerId)}
        >
          <option value="">Choose…</option>
          {customers
            .filter((customer) => customer.isActive)
            .map((customer) => (
              <option key={customer.id} value={customer.id}>
                {customer.name}
              </option>
            ))}
        </select>
      </label>
      {errors.customerId && <p className="field-error">{errors.customerId}</p>}
      <label>
        Subject
        <input
          value={subject}
          onChange={(event) => setSubject(event.target.value)}
          aria-invalid={Boolean(errors.subject)}
        />
      </label>
      {errors.subject && <p className="field-error">{errors.subject}</p>}
      <label>
        Priority
        <select
          value={priority}
          onChange={(event) => setPriority(event.target.value as Priority)}
        >
          <option>low</option>
          <option>normal</option>
          <option>high</option>
          <option>urgent</option>
        </select>
      </label>
      <button disabled={pending}>
        {pending ? "Saving…" : "Create ticket"}
      </button>
      <p role="status">{message}</p>
    </form>
  );
}
