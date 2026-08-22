.PHONY: setup up down reset test frontend-install frontend-dev frontend-test frontend-check frontend-build smoke routes migrate-status logs db-shell db-labs worker queue-status cache-clear dependency-mode resilience-labs
setup:
	./scripts/bootstrap
up:
	./scripts/dev
down:
	docker compose down
reset:
	./scripts/reset
test:
	./scripts/test
frontend-install:
	cd app && npm install
frontend-dev:
	cd app && npm run dev
frontend-test:
	cd app && npm run test:frontend
frontend-check:
	cd app && npm run typecheck && npm run lint && npm run format:check
frontend-build:
	cd app && npm run build
smoke:
	./scripts/smoke
routes:
	docker compose exec web php artisan route:list
migrate-status:
	docker compose exec web php artisan migrate:status
logs:
	docker compose logs -f web
db-shell:
	docker compose exec db mysql -urelaydesk -prelaydesk relaydesk
db-labs:
	docker compose exec web php artisan help lab:database
worker:
	docker compose up -d worker
	@echo "Worker is running; use: docker compose logs -f worker"
queue-status:
	docker compose exec web php artisan lab:resilience queue
cache-clear:
	docker compose exec web php artisan cache:clear
dependency-mode:
	@test -n "$(MODE)" || (echo "usage: make dependency-mode MODE=success|delay|transient|persistent|malformed|client-error"; exit 2)
	curl -fsS -X PUT -H 'Content-Type: application/json' -d '{"mode":"$(MODE)"}' http://localhost:$${DEPENDENCY_PORT:-8090}/mode
resilience-labs:
	docker compose exec web php artisan help lab:resilience
