import { render, screen, waitFor } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { MemoryRouter } from "react-router-dom";
import { expect, test } from "vitest";
import { App } from "../src/App";

test("filters rendered tickets from user input", async () => {
  const user = userEvent.setup();
  render(
    <MemoryRouter initialEntries={["/tickets"]}>
      <App />
    </MemoryRouter>,
  );
  expect(
    await screen.findByText("Cannot export the weekly report"),
  ).toBeVisible();
  await user.type(screen.getByLabelText("Filter tickets"), "migration");
  expect(
    screen.queryByText("Cannot export the weekly report"),
  ).not.toBeInTheDocument();
  expect(screen.getByText("Confirm migration window")).toBeVisible();
});
test("renders detail selected through URL state", async () => {
  render(
    <MemoryRouter initialEntries={["/tickets/1"]}>
      <App />
    </MemoryRouter>,
  );
  expect(
    await screen.findByRole("heading", {
      name: "Cannot export the weekly report",
    }),
  ).toBeVisible();
  expect(screen.getByText("Northwind Studio")).toBeVisible();
});
test("reports form errors before creating a ticket", async () => {
  const user = userEvent.setup();
  render(
    <MemoryRouter initialEntries={["/tickets/new"]}>
      <App />
    </MemoryRouter>,
  );
  await user.click(
    await screen.findByRole("button", { name: "Create ticket" }),
  );
  expect(screen.getByText("Choose a customer.")).toBeVisible();
  expect(screen.getByText("Use at least 5 characters.")).toBeVisible();
});
test("creates a valid local ticket and renders it", async () => {
  const user = userEvent.setup();
  render(
    <MemoryRouter initialEntries={["/tickets/new"]}>
      <App />
    </MemoryRouter>,
  );
  await user.selectOptions(await screen.findByLabelText("Customer"), "1");
  await user.type(screen.getByLabelText("Subject"), "Printer needs attention");
  await user.click(screen.getByRole("button", { name: "Create ticket" }));
  await waitFor(() =>
    expect(screen.getByRole("status")).toHaveTextContent("Ticket added"),
  );
});
