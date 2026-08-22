.PHONY: setup up down reset test smoke routes migrate-status logs
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
smoke:
	./scripts/smoke
routes:
	docker compose exec web php artisan route:list
migrate-status:
	docker compose exec web php artisan migrate:status
logs:
	docker compose logs -f web
