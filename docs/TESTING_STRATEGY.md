# RelayDesk testing strategy

Tests are executable evidence about expected behavior. We select a level from the risk and disputed boundary, not from a coverage target.

RelayDesk deliberately installs PHPUnit directly and uses `vendor/bin/phpunit` as its backend test runner. The minimal application does not install Collision, which supplies Laravel's optional `artisan test` command. `composer test` is a convenience wrapper around the same PHPUnit executable, so CI, containers, chapter commands, and Composer all exercise one interface.

| Level | Proves here | Does not prove | Command |
| --- | --- | --- | --- |
| Unit | A domain decision in PHP without booting Laravel | SQL, wiring, or HTTP | `make test-unit` |
| Database/integration | Eloquent mappings, constraints, transactions, tenant invariants on real storage | Public JSON or browser behavior | `make test-integration` |
| API | Authentication, authorization, status, stable JSON fields, headers, and side effects | React presentation | `make test-api` |
| React | User action produces visible loading, validation, error, navigation, and permission results | Browser/server integration | `make frontend-test` |
| E2E | A few critical journeys cross browser, session, API, and MySQL | Precise diagnosis of a failure | `make test-e2e` |

## Determinism and data

Backend tests use `RefreshDatabase`; each test creates its own organization, membership, and owned records. `Tests\\TestCase::member()` makes the security boundary explicit rather than relying on seed order. SQLite gives fast feedback, while the clean Compose run repeats the suite on MySQL and exercises MySQL-only composite foreign keys. `make reset` destroys the volume, migrates, and seeds a known local dataset.

Use factories or small builders when a shape repeats; prefer named records relevant to the assertion over large fixtures. Never depend on an auto-increment value unless the contract is specifically about that value. Tests must not depend on execution order.

## Boundary policies

API assertions cover statuses and important fields, not complete payload snapshots. Contract tests deliberately protect `customerId`, `requestId`, `Location`, `Idempotency-Replayed`, tenant denial, and persistent side effects. React tests use roles, labels, text, and user events; they avoid component internals, CSS selectors, and snapshots as a primary strategy.

E2E is limited to admin creation/persistence and viewer denial. It runs serially against reset seed data and waits for observable DOM conditions—never arbitrary sleeps. A broad failure begins localization: E2E → API → database/integration → unit.

External HTTP is isolated with Laravel `Http::fake()` in automated tests; the repository simulator is for live boundary and E2E evidence. Queues use `Queue::fake()` when dispatch count is the behavior and invoke a job synchronously with a fake dependency when job effects are the behavior. Concurrency tests use two known snapshots and optimistic versions or the database lab's explicit barrier, never timing luck. Relative performance evidence avoids machine-specific millisecond thresholds.

## Failure injection

Ordinary behavior is the default. Explicit headers cover one request; simulator modes cover one dependency; `LAB_PROFILE=debug-capstone` activates the Chapter 58 multi-defect profile only in local/testing environments. `make debug-capstone` activates it and `make reset` removes it. The reader exercise does not identify defect locations; instructor notes live under `book/solutions/`.
