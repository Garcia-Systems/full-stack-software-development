# Chapter 57: End-to-End Tests

[Previous](56-react-tests.md) · [Next](58-full-stack-debugging-method.md)

Playwright covers only high-value journeys through a real browser and running RelayDesk:

1. an admin session opens new-ticket, creates work, reloads, and sees the persisted subject;
2. a viewer session opens tickets and cannot see the create action.

```sh
make reset
cd app && npx playwright install chromium
E2E_BASE_URL=http://localhost:8080 npm run test:e2e
```

The API request context establishes the seeded session; the browser then exercises React, cookies, API calls, authorization-aware presentation, Laravel, and MySQL. Tests run serially with unique subjects, use accessible locators, and wait for visible conditions rather than sleeps. Traces are retained on failure.

E2E is slower, broader, environment-sensitive, and less diagnostically precise. A failure proves the workflow failed. It does **not** prove “the backend is broken.” Inspect the Playwright trace and request ID, reproduce the API call, then narrow with API, database, and unit tests.

A background journey is deliberately omitted: simulator and worker behavior already has deterministic job/API coverage, while polling a live worker would add cost and environmental sensitivity disproportionate to its evidence. This is a deliberate scope decision, not a missing test count.

Reset before E2E; do not share unexplained state or retry until green. CI may retry once to capture environment-sensitive trace evidence, but a retry is not permission to normalize flakes.

**Exit proof:** `scripts/lab chapter 57 verify` proves both journeys against known seed data.
