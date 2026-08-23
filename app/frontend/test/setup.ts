import "@testing-library/jest-dom/vitest";
import { cleanup } from "@testing-library/react";
import { afterEach } from "vitest";

afterEach(cleanup);

const testTickets = [
  {
    id: 1,
    customerId: 1,
    subject: "Cannot export the weekly report",
    description: "CSV export remains pending.",
    status: "open",
    priority: "high",
    version: 1,
    createdAt: "2026-01-01T00:00:00.000Z",
  },
  {
    id: 2,
    customerId: 1,
    subject: "Confirm migration window",
    status: "in_progress",
    priority: "normal",
    version: 1,
    createdAt: "2026-01-01T00:00:00.000Z",
  },
];
Object.defineProperty(globalThis, "fetch", {
  writable: true,
  value: async (_input: RequestInfo | URL, init?: RequestInit) =>
    String(_input).endsWith("/session")
      ? new Response(
          JSON.stringify({
            user: { name: "Alice", memberships: [{ role: "admin" }] },
          }),
          { status: 200, headers: { "Content-Type": "application/json" } },
        )
      : new Response(
          JSON.stringify(
            init?.method === "POST"
              ? {
                  data: {
                    ...JSON.parse(String(init.body)),
                    id: 9,
                    status: "open",
                    version: 1,
                  },
                  requestId: "frontend-test",
                }
              : { data: testTickets, requestId: "frontend-test" },
          ),
          {
            status: init?.method === "POST" ? 201 : 200,
            headers: { "Content-Type": "application/json" },
          },
        ),
});
