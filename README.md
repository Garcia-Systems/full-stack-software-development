# Full-Stack Software Engineering: Build, Break, Debug, Deploy

This repository accompanies an evidence-first book. Part I is executable now: a browser page lists and creates RelayDesk support tickets through a transparent PHP endpoint backed by MySQL.

## Part I in five minutes

### Prerequisites

- Git, `curl`, and a current browser with developer tools.
- Docker Engine with Compose v2 (Docker Desktop or WSL 2 is suitable). Allocate about 2 GB RAM and 2 GB free disk.
- `make` is convenient; every target delegates to a script and is optional.

```sh
make setup       # validates Docker and creates .env
make up          # builds and starts PHP + MySQL; waits until ready
```

Visit <http://localhost:8080>. Add a ticket, then inspect the request in DevTools **Network**. The JSON API is at <http://localhost:8080/api/tickets>.

```sh
make test        # PHP behavior, syntax, docs, and live MySQL smoke tests
make reset       # destroys local database volume and restores two seed tickets
make down        # stops services but preserves ticket data
make smoke       # checks the live vertical path
```

`make reset` deletes Part I data. `make down` does not. Change `APP_PORT` in `.env` if port 8080 is occupied. Inspect processes and logs with `docker compose ps` and `docker compose logs -f web`.

## Read and run

Start with [Chapter 1](book/chapters/01-full-stack-means-boundaries.md), then follow the chapter navigation through [Chapter 8](book/chapters/08-build-the-smallest-full-stack.md). Each chapter has an observation, controlled failure, evidence prompts, repair, and verification. The stable lab interface is:

```sh
scripts/lab chapter 4 setup
scripts/lab chapter 4 verify
scripts/lab chapter 4 reset
```

The implementation intentionally is **not Laravel or React**. The approved [book plan](docs/BOOK_PLAN.md) makes this transparent PHP/MySQL/browser slice disposable; Chapter 9 will replace it with Laravel. Named faults work only when `APP_ENV=local` and `LAB_FAULTS=true`.

## Troubleshooting

- A `curl: (7)` error means no connection was established; check `docker compose ps` and the port.
- An HTTP `500` or `503` proves a server answered; copy `X-Request-ID` and search `docker compose logs web`.
- On Apple Silicon, the official images used here are multi-platform. Windows readers should run commands inside WSL 2.
- Run `docker compose config` to inspect resolved configuration without starting anything.
