# Tickets Manager

A minimal tickets management backend built with Symfony 7 and Doctrine ORM. It exposes a small REST API to manage organisers, events, and tickets, and ships with OpenAPI/Swagger documentation via NelmioApiDocBundle.

## Stack
- Language: PHP (>= 8.4.14)
- Framework: Symfony 7.3 (skeleton)
- ORM/DB: Doctrine ORM + Migrations + Fixtures
- API Docs: NelmioApiDocBundle (Swagger UI at `/api/doc`)
- Runtime: Symfony Runtime; FrankenPHP scaffolding present (`backend/frankenphp/`)
- Package manager: Composer
- Database: PostgreSQL (connection via `DATABASE_URL`)

## Project Layout
```
./
├── backend/                     # Symfony application (API)
│   ├── composer.json            # PHP dependencies & scripts
│   ├── public/index.php         # HTTP entry point
│   ├── src/
│   │   ├── Controller/          # HTTP controllers (routes via attributes)
│   │   ├── Entity/              # Doctrine entities
│   │   ├── Events/              # Bounded context: Events (Domain, App, Infra)
│   │   ├── Organisers/          # Bounded context: Organisers
│   │   ├── Tickets/             # Bounded context: Tickets
│   │   └── Shared/              # Shared kernel/utilities
│   ├── migrations/              # Doctrine migrations
│   ├── src/DataFixtures/        # Development/test fixtures
│   ├── config/routes.yaml       # Attribute routes loader for controllers
│   ├── config/routes/nelmio_api_doc.yaml  # OpenAPI/Swagger endpoints
│   ├── .env                     # Default env variables
│   └── frankenphp/              # FrankenPHP helper scripts (Docker entrypoint)
├── frontend/                    # TODO: Document stack & setup (no package.json yet)
├── pgdata/                      # Postgres data dir (if used locally)
└── LICENSE                      # MIT
```

## Requirements
- PHP >= 8.4.14
- Composer
- PostgreSQL (recommended 16 per default `serverVersion`, configurable)
- Optional: Symfony CLI (for convenient local server)

## Configuration (Environment Variables)
Defined in `backend/.env` and overridable via real environment variables:
- `APP_ENV` (default: `dev`) — Symfony environment.
- `APP_SECRET` — Application secret (set in non-dev environments).
- `DEFAULT_URI` — Base URI used when generating URLs in CLI contexts.
- `DATABASE_URL` — Doctrine DBAL connection string (PostgreSQL by default). Example:
  - `postgresql://appUser:test123qwerty@database:5432/app?serverVersion=16&charset=utf8`
- `CORS_ALLOW_ORIGIN` — Regex for allowed origins (for NelmioCorsBundle), default allows localhost.

Never commit production secrets in `.env`. For production, use real environment variables and/or Symfony secrets.

## Installation (Backend)
From the repository root:

```bash
cd backend
composer install
```

Copy the default environment config and adjust values as needed:

```bash
cp .env .env.local   # edit .env.local
```

### Database Setup
Make sure PostgreSQL is running and the user/database in `DATABASE_URL` exist. Then run migrations:

```bash
php bin/console doctrine:migrations:migrate
```

(Optional) Load sample data (fixtures):

```bash
php bin/console doctrine:fixtures:load
```

## Running the API Locally

```bash
cd backend
docker composer up --wait
```

## OpenAPI/Swagger:

- `GET /api/doc` — Swagger UI
- `GET /api/doc.json` — OpenAPI JSON


## Useful Commands
- Clear cache: `php bin/console cache:clear`
- List routes: `php bin/console debug:router`
- Validate schema: `php bin/console doctrine:schema:validate`
- Run migrations: `php bin/console doctrine:migrations:migrate`
- Load fixtures: `php bin/console doctrine:fixtures:load`

## Scripts
Composer defines Symfony Flex auto-scripts (cache clear, assets install) executed on install/update. There are no custom Composer scripts in `backend/composer.json` beyond Flex defaults.

## Tests
A `tests/` namespace is configured in Composer, but no test files were found in this repository.
- TODO: Add test suite (e.g., PHPUnit) and document how to run it.

## License
This project is licensed under the MIT License — see `LICENSE` at the repository root.

## Troubleshooting
- Database connection issues: verify `DATABASE_URL` and that PostgreSQL is reachable; the FrankenPHP entrypoint script shows how DB readiness is typically checked in Docker.
- CORS: adjust `CORS_ALLOW_ORIGIN` in `backend/.env` and the Nelmio CORS config if calling the API from a browser.
