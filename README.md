# Tickets Manager

A minimal tickets management backend built with Symfony 7 and Doctrine ORM. It exposes a small REST API to manage organisers, events, and tickets, and ships with OpenAPI/Swagger documentation via NelmioApiDocBundle.

## Stack
- Backend:
  - Language: PHP (>= 8.4.14)
  - Framework: Symfony 7.3 (skeleton)
  - ORM/DB: Doctrine ORM + Migrations + Fixtures
  - API Docs: NelmioApiDocBundle (Swagger UI at `/api/doc`)
  - Security: Symfony Security (stateless) + JWT (HS256)
  - Auth: Google Sign-In (frontend-driven ID token verification)
  - Runtime: Symfony Runtime; FrankenPHP scaffolding present (`backend/frankenphp/`)
  - Package manager: Composer
  - Database: PostgreSQL (connection via `DATABASE_URL`)
- Frontend (present in repo):
  - Vue 3 + Vite
  - State: Pinia
  - Routing: Vue Router
  - Language: TypeScript
  - Note: package manager/scripts are not committed — see TODO below.

## Project Layout
```
./
├── backend/                     # Symfony application (API)
│   ├── composer.json            # PHP dependencies & scripts
│   ├── public/index.php         # HTTP entry point
│   ├── src/
│   │   ├── Events/              # Bounded context: Events (Domain, App, Infra)
│   │   ├── Organisers/          # Bounded context: Organisers
│   │   ├── Tickets/             # Bounded context: Tickets
│   │   ├── Users/               # Bounded context: Users (System User)
│   │   ├── Auth/                # Auth (Google Sign-In flow, controllers)
│   │   └── Shared/              # Shared kernel/utilities (e.g., Security/JWT)
│   ├── migrations/              # Doctrine migrations
│   ├── src/DataFixtures/        # Development/test fixtures
│   ├── config/routes.yaml       # Attribute routes loader for controllers
│   ├── config/routes/nelmio_api_doc.yaml  # OpenAPI/Swagger endpoints
│   ├── .env                     # Default env variables
│   └── frankenphp/              # FrankenPHP helper scripts (Docker entrypoint)
├── frontend/                    # Vue 3 app (Vite + Pinia + Vue Router)
└── LICENSE                      # MIT
```

## Requirements
- Backend: PHP >= 8.4.14, Composer, PostgreSQL (recommended 16 per default `serverVersion`, configurable)
- Optional: Symfony CLI (for convenient local server)
- Frontend (when configured): Node.js 18+ and a package manager (npm/pnpm/yarn) — TODO: add `package.json` and scripts

## Configuration (Environment Variables)
Backend variables are defined in `backend/.env` and overridable via real environment variables:
- `APP_ENV` (default: `dev`) — Symfony environment.
- `APP_SECRET` — Application secret (set in non-dev environments).
- `DEFAULT_URI` — Base URI used when generating URLs in CLI contexts.
- `DATABASE_URL` — Doctrine DBAL connection string (PostgreSQL by default). Example:
  - `postgresql://appUser:test123qwerty@database:5432/app?serverVersion=16&charset=utf8`
- `CORS_ALLOW_ORIGIN` — Regex for allowed origins (for NelmioCorsBundle), default allows localhost.
- `JWT_SECRET` — Secret used to sign JWTs (required for auth; use a strong random string in non-dev).
- `JWT_TTL` — Optional token TTL in seconds (default: 3600 if not set).
- `GOOGLE_CLIENT_ID` — Google OAuth client ID used to verify frontend ID tokens.

Frontend variables (used by Vite) are in `frontend/.env`:
- `VITE_API_BASE_URL` — Base URL of the backend API that the frontend will call (e.g., `http://127.0.0.1:8000`).
- `BASE_URL` — Optional Vite base path for deploying under a sub-path.

Never commit production secrets in `.env`. For production, use real environment variables and/or Symfony secrets.

## Installation (Backend)
From the repository root:

```bash
cd backend
docker exec -ti backend-php-1 composer install
```

Copy the default environment config and adjust values as needed:

```bash
cp .env .env.local   # edit .env.local
```

### Database Setup
Make sure PostgreSQL is running and the user/database in `DATABASE_URL` exist. Then run migrations:

```bash
docker exec -ti backend-php-1 php bin/console doctrine:migrations:migrate
```

(Optional) Load sample data (fixtures):

```bash
docker exec -ti backend-php-1 php bin/console doctrine:fixtures:load
```

## Running the API Locally

- Backend 
  ```bash
  cd backend && 
  docker compose up --wait
  ```
- Frontend
  ```bash
  cd frontend &&
  npm run dev
  ```

## OpenAPI/Swagger
- Swagger UI: `GET /api/doc`
- OpenAPI JSON: `GET /api/doc.json`


## Useful Commands
- Clear cache: `php bin/console cache:clear`
- List routes: `php bin/console debug:router`
- Validate schema: `php bin/console doctrine:schema:validate`
- Run migrations: `php bin/console doctrine:migrations:migrate`
- Load fixtures: `php bin/console doctrine:fixtures:load`

## Scripts
Composer defines Symfony Flex auto-scripts (cache clear, assets install) executed on install/update. There are no custom Composer scripts in `backend/composer.json` beyond Flex defaults.

## Tests
A `tests/` namespace is configured in Composer, but no backend test files were found in this repository.
- TODO: Add a PHPUnit test suite and document how to run it.
- TODO: Add frontend unit tests (e.g., Vitest) once the frontend toolchain is committed.

## License
This project is licensed under the MIT License — see `LICENSE` at the repository root.

## Troubleshooting
- Database connection issues: verify `DATABASE_URL` and that PostgreSQL is reachable; the FrankenPHP entrypoint script shows how DB readiness is typically checked in Docker.
- CORS: adjust `CORS_ALLOW_ORIGIN` in `backend/.env` and the Nelmio CORS config if calling the API from a browser.
- Frontend cannot reach API: check `frontend/.env` → `VITE_API_BASE_URL` and browser console/network tab.
