.PHONY: setup up down reset test test-backend test-unit test-integration test-api test-e2e frontend-install frontend-dev frontend-test frontend-check frontend-build smoke routes migrate-status logs db-shell db-labs worker queue-status cache-clear dependency-mode resilience-labs debug-capstone production-build production-deploy production-verify production-stop backup restore-verify
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
test-backend:
	docker compose exec web php artisan test
test-unit:
	docker compose exec web php artisan test tests/Unit
test-integration:
	docker compose exec web php artisan test tests/Feature/DatabaseIntegrityTest.php tests/Feature/ProjectTransactionTest.php tests/Feature/OptimisticConcurrencyTest.php tests/Feature/RelationshipQueryTest.php
test-api:
	docker compose exec web php artisan test tests/Feature/PartFiveApiTest.php tests/Feature/PartSixResilienceTest.php
test-e2e:
	cd app && npm run test:e2e
debug-capstone:
	LAB_PROFILE=debug-capstone docker compose up -d --force-recreate web worker
	@echo "Debug capstone active. Run make reset to return to normal."
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
production-build:
	./scripts/production build
production-deploy:
	./scripts/production deploy
production-verify:
	./scripts/production verify
production-stop:
	./scripts/production stop
backup:
	./scripts/backup
restore-verify:
	@test -n "$(FILE)" || (echo "usage: make restore-verify FILE=backups/file.sql"; exit 2)
	./scripts/restore-verify "$(FILE)"
