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

1. Copy `.env.example` to `.env` and update the database credentials.
2. Install dependencies:

```bash
composer install
```

3. Create the database and run migrations:

```bash
composer migrate
```

4. Seed the database:

```bash
composer seed
```

5. Serve the API from the `public/` directory. Example using PHP built-in server:

```bash
php -S localhost:8000 -t public
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

After copying `.env` and running `composer install`, run migrations and seeders:

```bash
php database/migrate.php
php database/seed.php
```

If you prefer the composer scripts defined in `composer.json`:

```bash
composer migrate
composer seed
```

If migration or seeding fails due to database connection, verify your `.env` settings and that the database exists.
