# 6. The Server Is a Process

[Previous](05-addressing.md) · [Next](07-persistent-state.md)

“The server” here is a PHP CLI process listening on `0.0.0.0:8080` inside a container. Compose starts and supervises it; PHP writes request logs to standard error.

## Inspect its lifecycle

```sh
docker compose ps
docker compose top web
docker compose logs --tail=20 web
docker compose stop web
curl -v --connect-timeout 2 http://localhost:8080/api/tickets
docker compose start web
curl -i http://localhost:8080/api/tickets
```

Stopping only `web` leaves MySQL running. The failed curl has no HTTP response; after restart, PHP answers and the previous data remains.

Inspect the exact command and environment:

```sh
docker compose exec web sh -c 'tr "\0" " " </proc/1/cmdline; echo'
docker compose exec web sh -c 'printf "APP_ENV=%s DB_HOST=%s\n" "$APP_ENV" "$DB_HOST"'
```

## Port collision

While RelayDesk is up, run `php -S 127.0.0.1:8080 -t app/public`. It should report that the address is already in use. That process never becomes a listener. Stop it with Ctrl-C if your platform starts it (for example, if you changed `APP_PORT`).

To change the published host port, set `APP_PORT=8090` in `.env`, run `docker compose up -d --force-recreate web`, and use port 8090. Container PHP still listens at 8080. Restore `.env` afterward. Process state (`docker compose ps`), listener configuration, logs, and an HTTP probe are independent evidence.
