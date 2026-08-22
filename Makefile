.PHONY: setup up down reset test frontend-install frontend-dev frontend-test frontend-check frontend-build smoke routes migrate-status logs db-shell db-labs
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
