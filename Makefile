.PHONY: setup up down reset test smoke
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
