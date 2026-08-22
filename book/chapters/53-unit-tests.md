# Chapter 53: Unit Tests

[Previous](52-what-should-be-tested.md) · [Next](54-database-integration-tests.md)

A useful unit boundary tests a small behavior without requiring the entire application environment. `TicketWorkflowTest` constructs a ticket and proves that closed is terminal; no router, database, or mock repository is necessary.

## Arrange, act, assert

Arrange a closed ticket. Act by requesting `open`. Assert the meaningful business rejection. The test fails if someone permits the transition—not because a getter or framework detail changed.

```sh
docker compose exec web php artisan test tests/Unit
```

## Regression exercise

1. Temporarily remove the terminal-state guard in `TicketWorkflow::transition`.
2. Run the unit command and observe the failure.
3. State the expected rule: closed work cannot return to open.
4. Restore the smallest guard.
5. Rerun the unit test, then `make test-api` because HTTP also exposes the rule.

Ask what the failure proves: the isolated rule did not reject the transition. It does not prove that a route calls this service or that persistence is correct.

Use doubles only at volatile boundaries. A retry decision may accept an exception/status as input without contacting a vendor. Do not mock the ticket, ORM, logger, request, and framework merely to make a controller look isolated; that creates a test of mock wiring. Plain domain objects and values are often stronger.

Good unit targets include permissions that are separable, state transitions, transformations, and retry/idempotency decisions. Avoid “getter returns assigned value.” That mirrors implementation without protecting a business outcome.

**Exit proof:** `scripts/lab chapter 53 verify` is fast, deterministic, and fails for a business reason.
