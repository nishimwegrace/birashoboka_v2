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

logs:
	$(DC) logs -f

shell:
	$(DC) exec app bash

migrate:
	$(DC) exec app php database/migrate.php

seed:
	$(DC) exec app php database/seed.php
