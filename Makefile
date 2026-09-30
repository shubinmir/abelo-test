install:
	[ -f .env ] || cp .env.example .env
	docker compose up -d --build
	docker compose exec php composer install
	docker compose exec php composer build-css
	docker compose exec php composer seed

up:
	docker compose up -d

down:
	docker compose down

seed:
	docker compose exec php composer seed -- --articles=$(or $(ARTICLES),60)

css:
	docker compose exec php composer build-css

.PHONY: install up down seed css
