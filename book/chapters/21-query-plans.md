# 21. Ask MySQL for the Query Plan

[Previous](20-indexes.md) · [Next](22-transactions.md)

A query is not optimized because its PHP looks tidy. Ask MySQL how it actually runs.

```sh
docker compose exec web php artisan lab:database seed-performance
docker compose exec web php artisan lab:database plan --without-index
docker compose exec web php artisan lab:database plan
```

MySQL 8.4 `EXPLAIN ANALYZE` executes the statement and reports iterator estimates, actual rows, loops, and timing. Plain `EXPLAIN` is useful when execution is unsafe or expensive, but its row counts are estimates. In tabular plans, inspect access `type`, `key`, estimated `rows`, and join order; `ALL` commonly indicates a table scan, while `ref`/`range` describe narrower access. Read these as evidence, not a scorecard. [MySQL documents EXPLAIN output](https://dev.mysql.com/doc/refman/8.4/en/explain-output.html) and [EXPLAIN ANALYZE](https://dev.mysql.com/doc/refman/8.4/en/explain.html#explain-analyze).

## Performance debugging exercise

You receive only: “the urgent-work dashboard slowed after data import.” Use this sequence and record output:

1. **Symptom:** reproduce the dashboard predicate against the deterministic data.
2. **Identify query:** capture the SQL/bindings, not merely the controller method.
3. **Inspect plan:** run without the workload index.
4. **Hypothesis:** a composite lookup will reduce examined rows for this predicate.
5. **Change:** restore `(organization_id, priority, created_at)`.
6. **Remeasure:** compare plan rows/iterator path and repeated timings.
7. **Reject alternatives:** explain why an index on `status` or reversed date-first order does not match this equality-then-range workload as well.

The lab changes one variable. It does not prove the query is optimal for every production distribution. Restore the index with `php artisan lab:database index` before continuing.
