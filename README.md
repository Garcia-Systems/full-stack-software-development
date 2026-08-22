# Full-Stack Software Engineering: Build, Break, Debug, Deploy

This repository accompanies an evidence-first book. Parts I–VI are executable. Part VI adds cached dashboard summaries, durable background work, a deterministic HTTP dependency, bounded failure policy, idempotent ticket creation, cross-worker race evidence, structured correlation, and measurement without adding a production-scale platform.

## Run RelayDesk

Prerequisites are Git, `curl`, a browser, Docker Engine/Compose v2, and approximately 2 GB RAM/disk.

```sh
make setup                 # validate tooling and create .env
make up                    # build Laravel + MySQL and wait for the Laravel API
make test                  # syntax, PHPUnit, docs, clean-database live smoke
make reset                 # delete the volume, migrate, and seed known data
make down                  # stop containers but preserve rows
make smoke                 # exercise live CRUD and validation
make routes                # inspect dispatch
make migrate-status        # inspect schema history
make logs                  # follow structured logs/request IDs
make db-shell              # open MySQL directly
make db-labs               # list Part III evidence commands
make worker                # start/restart the database queue worker
make queue-status          # inspect ready/reserved/failed work
make cache-clear           # clear the second copy of dashboard state
make dependency-mode MODE=transient # control the local HTTP dependency
make resilience-labs       # list the Part VI evidence harness
make frontend-install      # install the pinned Node dependency tree
make frontend-dev          # Vite development server at http://localhost:5173
make frontend-test         # behavior tests in jsdom
make frontend-check        # TypeScript, ESLint, and Prettier checks
make frontend-build        # production assets consumed by Laravel
```

Visit <http://localhost:8080> for the built SPA. It uses the versioned `/api/v1` JSON contract; log in with the seeded local-only Alice account (`alice@relaydesk.test` / `password`). The unversioned API remains for Chapters 9–24 checkpoint exercises. Normal database seed data remains intentionally small, and the opt-in performance seeder adds exactly 20,000 deterministic tickets.

Start with [Chapter 1](book/chapters/01-full-stack-means-boundaries.md) and follow navigation through [Chapter 51](book/chapters/51-performance-across-stack.md). Part I's disposable browser is retained only as a learning artifact; the Laravel root now serves the compiled React application with a direct-navigation fallback.

## Frontend workflow

Use Node 20.19 or newer (the Docker build uses Node 20). For fast browser feedback, run `make up` for Laravel/MySQL and `make frontend-dev` in another terminal, then use the Vite URL. The frontend uses a small typed fetch client with session credentials. Vite proxies `/api` to Laravel; direct-origin CORS exercises use `VITE_API_URL=http://localhost:8080/api/v1`. Deterministic failure headers require local `LAB_FAULTS=true`. Run `make frontend-check frontend-test frontend-build` before committing. A reload resets fixture mutations.

The only runtime libraries added are React/React DOM and React Router: React owns rendering/state, and multiple justified URL-addressable pages justify routing. Vite/TypeScript, Testing Library/Vitest, ESLint, and Prettier are development/build tools. No global state, form, UI, or data-fetching library is used.

## Reproducible state and evidence

`make reset` destroys local data, applies every migration to an empty MySQL database, and loads deterministic teaching data. For direct inspection:

```sh
docker compose exec web php artisan migrate:status
docker compose exec db mysql -urelaydesk -prelaydesk relaydesk -e 'SHOW TABLES;'
docker compose exec db mysql -urelaydesk -prelaydesk relaydesk -e 'SHOW CREATE TABLE tickets\G'
docker compose exec db mysql -urelaydesk -prelaydesk relaydesk -e 'SHOW INDEX FROM tickets;'
docker compose exec web php artisan lab:database plan
docker compose exec web php artisan lab:database integrity
docker compose exec web php artisan test
docker compose logs -f web
```

Send `X-Request-ID` with curl; safe IDs cross Laravel middleware into the response and structured log context. Local labs use explicit requests listed in `labs/catalog.yaml`, never hidden production behavior.

## Part VI resilience workflow

The reference stack deliberately uses Laravel's **database cache and database queue**. They make the extra copies, leases, attempts, and failures visible with the MySQL skills readers already have and avoid adding Redis before workload evidence justifies operating it. `worker` runs the same application image as `web`; `dependency` is a tiny PHP HTTP simulator, not a trusted in-process fake.

```sh
make dependency-mode MODE=transient
docker compose logs -f worker dependency
make queue-status
docker compose exec web php artisan lab:resilience cache
docker compose exec web php artisan lab:resilience race
docker compose exec web php artisan lab:resilience performance
docker compose exec web php artisan lab:resilience incident
```

Ticket creation optionally accepts `Idempotency-Key`; the SPA always sends one per submission. Simulator controls are local-only and support `success`, `delay`, `transient` (two 503 responses then success), `persistent`, `malformed`, and `client-error`. Use `make reset` to clear durable application/cache/queue state and restore success mode.

## Part III database workflow

Run `make reset` for the small fixture, and opt into the repeatable workload with `docker compose exec web php artisan lab:database seed-performance`. Use `php artisan help lab:database` to inspect SQL/bindings, count relationship queries, compare an index and query plan, inject a transaction failure, replay a deterministic lost update, and list constraints. Index experiments may temporarily drop the teaching index; run `php artisan lab:database index` or `make reset` to restore the migrated schema.

## Troubleshooting

- `curl: (7)` means no HTTP connection; inspect `docker compose ps`.
- A response proves the server boundary was reached. Use status/body and `X-Request-ID` before reading the matching log.
- A `422`, `404`, `409`, and `500` have deliberately different owners; Chapter 17 supplies the lab.
- If port 8080 is occupied, set `APP_PORT` in `.env` and recreate `web`.
