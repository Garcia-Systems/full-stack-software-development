import { expect, test } from "@playwright/test";

async function login(
  request: import("@playwright/test").APIRequestContext,
  email: string,
) {
  const response = await request.post("/api/v1/session", {
    data: { email, password: "password" },
  });
  expect(response.ok()).toBeTruthy();
}

test(
  "admin logs in, creates a ticket, and sees the persisted result",
  async ({ page, request }) => {
    await login(request, "alice@relaydesk.test");
    const subject = `E2E evidence ${Date.now()}`;
    await page.goto("/tickets/new");
    await page.getByLabel("Customer").selectOption("1");
    await page.getByLabel("Subject").fill(subject);
    await page.getByRole("button", { name: "Create ticket" }).click();
    await expect(page.getByRole("status")).toContainText("Ticket added");
    await page.reload();
    await page.goto("/tickets");
    await expect(page.getByText(subject)).toBeVisible();
  },
);

test("viewer cannot see the create-ticket action", async ({ page, request }) => {
  await login(request, "viewer@relaydesk.test");
  await page.goto("/tickets");
  await expect(page.getByRole("link", { name: "New ticket" })).toHaveCount(0);
});
