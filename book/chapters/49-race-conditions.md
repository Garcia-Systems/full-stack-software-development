# Chapter 49: Races Across Requests and Workers

> Part VI — Build Systems That Survive Reality

## Beyond the lost-update lesson

Chapter 23 raced two edits to one row. Here two asynchronous actors both believe a notification delivery is absent and try to claim the same logical job. A naïve **check, then act** crosses a scheduling gap. Both checks can truthfully observe absence; neither observation reserves ownership.

```text
A: check delivery absent
B: check delivery absent
A: insert claim
B: insert claim
```

Run `docker compose exec web php artisan lab:resilience race`. The harness fixes the interleaving: both checks precede either act. Actor A inserts; actor B hits the unique `job_id` constraint. Exactly one claim remains. The exception is not proof that “the database failed”; it is the constraint correctly arbitrating ownership.

The mitigation fits the invariant: a job UUID may own at most one integration delivery. The database unique constraint is atomic and remains authoritative across PHP processes. A process-local boolean or cache check would not. If the scenario required temporarily exclusive mutable work rather than permanent uniqueness, a transactional row lock or atomic conditional update might fit better.

## Five-dimensional review

Value: same intended ticket event. Boundary: two workers to MySQL. Ownership: unique job claim. Timing: checks interleave before inserts. Count: exactly one delivery. Duplicate *delivery by a remote provider* is a separate boundary and requires provider idempotency.

This harness does not repeat Chapter 23's optimistic version example. It makes cross-process ownership deterministic and lets MySQL enforce the stable invariant.

Reference: [MySQL InnoDB locking](https://dev.mysql.com/doc/refman/8.4/en/innodb-locking.html).

---

[← Chapter 48](./48-idempotency.md) · [Chapter 50 →](./50-logging-observability.md)
