DC := docker compose

.PHONY: build up down ps logs shell migrate seed

build:
	$(DC) build --pull --no-cache

up:
	$(DC) up --build -d

down:
	$(DC) down

ps:
	$(DC) ps

restart:
	$(DC) restart

logs:
	$(DC) logs -f

shell:
	$(DC) exec app bash

migrate:
	$(DC) exec app php database/migrate.php

seed:
	$(DC) exec app php database/seed.php

admin:
	$(DC) exec app php database/see_admin.php

refresh:
	$(DC) exec app php database/migrate.php --fresh

init:
	@test -f .env || cp .env.example .env && echo ".env created from .env.example"
