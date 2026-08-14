# Birashoboka API

A vanilla PHP REST API using Eloquent ORM and MySQL/MariaDB. This project is built without Laravel, but it follows a Laravel-compatible architecture.

## Features

- PHP 8.3+
- Composer
- Eloquent ORM (`illuminate/database`)
- Dotenv configuration
- Token-based authentication
- Image upload and optimization via Intervention Image
- REST/JSON API
- Database migrations and seeders
- Pagination, filtering, and search

## Installation

This project supports running locally with Docker (recommended) or directly on your host PHP environment.

1) Create `.env` at the project root (an example is provided in the repository). Ensure at minimum:

```
DB_HOST=db
DB_PORT=3306
DB_DATABASE=birashoboka_v2
DB_USERNAME=root
DB_PASSWORD=rootsecret
APP_URL=http://localhost:8000
```

2) With Docker (recommended)

- Build and start services:

```bash
docker compose up -d --build
# or, using the provided Makefile
make up
```

- Run migrations (retries until DB ready) and seed:

```bash
make migrate
make seed
```

- Useful helpers:

```bash
make ps       # list containers
make logs     # follow logs
make shell    # open a shell in the app container
```

3) Without Docker

- Install PHP and Composer, then from project root:

```bash
composer install
php -S localhost:8000 -t public
php database/migrate.php
php database/seed.php
```

Notes about Composer: the Docker image is configured to not run `composer install` during image build by default. If you need Composer inside the container, run:

```bash
docker run --rm -v "$PWD":/app -w /app composer:2 install --no-interaction --prefer-dist --optimize-autoloader
```

## Environment variables

Required values in `.env`:

- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `APP_KEY`
- `IMAGE_MAX_WIDTH`
- `IMAGE_MAX_HEIGHT`
- `IMAGE_QUALITY`
- `CORS_ALLOWED_ORIGINS`

## Authentication

Use the `Authorization: Bearer <token>` header for protected endpoints.

### Auth endpoints

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET /api/auth/me`

## Example requests

### Register

```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"secret"}'
```

### Login

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"secret"}'
```

### Get volets with pagination

```bash
curl http://localhost:8000/api/volets?page=1&per_page=20
```

## Response format

All responses use this structure:

```json
{
  "success": true,
  "message": "...",
  "data": null
}
```

## Images

Uploaded testimonial images are processed and stored in `storage/uploads/testimonials`.

## Notes

- Passwords are hashed and never returned in responses.
- Tokens are stored securely on the user record.
- Only JSON responses are returned by endpoints.

## Running migrations and seeders

With Docker (recommended):

```bash
make migrate
make seed
```

Or run directly inside the app container:

```bash
docker compose exec app php database/migrate.php
docker compose exec app php database/seed.php
```

If you encounter a DB connection error, check:

- The app's runtime env values:

```bash
docker compose exec app php -r 'require "bootstrap.php"; echo "DB_HOST=".env("DB_HOST")." DB_USER=".env("DB_USERNAME")."\n";'
```

- MySQL logs and health:

```bash
docker compose logs --tail=200 db
docker compose exec db mysqladmin ping -uroot -prootsecret
```

To recreate a fresh database (warning: deletes data):

```bash
docker compose down -v
docker compose up -d --build
```
