# Full-Stack Software Engineering: Build, Break, Debug, Deploy

This repository accompanies an evidence-first book. Parts I–III are executable: the transparent PHP slice in Chapters 1–8 evolves into a Laravel/MySQL RelayDesk backend in Chapters 9–17, then Chapters 18–24 expose the SQL and database behavior below Eloquent.

## Run RelayDesk

Prerequisites are Git, `curl`, a browser, Docker Engine/Compose v2, and approximately 2 GB RAM/disk.

```sh
make setup                 # validate tooling and create .env
make up                    # build Laravel + MySQL and wait for /api/tickets
make test                  # syntax, PHPUnit, docs, clean-database live smoke
make reset                 # delete the volume, migrate, and seed known data
make down                  # stop containers but preserve rows
make smoke                 # exercise live CRUD and validation
make routes                # inspect dispatch
make migrate-status        # inspect schema history
make logs                  # follow structured logs/request IDs
make db-shell              # open MySQL directly
make db-labs               # list Part III evidence commands
```

Visit <http://localhost:8080> or inspect `GET /api/tickets`. The small browser from Part I still creates a ticket, now through Laravel validation, Eloquent, and the relational schema. Normal seed data remains intentionally small: two organizations, three customers (including an inactive one), one project, and two tickets. The opt-in performance seeder adds exactly 20,000 deterministic tickets.

Start with [Chapter 1](book/chapters/01-full-stack-means-boundaries.md) and follow navigation through [Chapter 24](book/chapters/24-data-integrity.md). Part I commands are preserved conceptually; its disposable router was intentionally replaced as forecast by Chapter 8 and the [book plan](docs/BOOK_PLAN.md).

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

## Part III database workflow

Run `make reset` for the small fixture, and opt into the repeatable workload with `docker compose exec web php artisan lab:database seed-performance`. Use `php artisan help lab:database` to inspect SQL/bindings, count relationship queries, compare an index and query plan, inject a transaction failure, replay a deterministic lost update, and list constraints. Index experiments may temporarily drop the teaching index; run `php artisan lab:database index` or `make reset` to restore the migrated schema.

## Troubleshooting

- `curl: (7)` means no HTTP connection; inspect `docker compose ps`.
- A response proves the server boundary was reached. Use status/body and `X-Request-ID` before reading the matching log.
- A `422`, `404`, `409`, and `500` have deliberately different owners; Chapter 17 supplies the lab.
- If port 8080 is occupied, set `APP_PORT` in `.env` and recreate `web`.
