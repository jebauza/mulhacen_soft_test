# REST API Application (Laravel 12)

Backend project built with Laravel, JWT authentication, and automated tests.

Minimum requirements
- PHP 8.4+, Composer 2, MariaDB 10.11 or Docker

Quick start (Docker)
1. Copy env: `cp .env.example .env`
2. Start containers: `docker-compose up -d --build`
3. Initialize app: `docker-compose exec backserver php artisan app:init --all`
4. Generate JWT: `docker-compose exec backserver php artisan jwt:secret`

Local setup (without Docker)
1. `composer install`
2. `cp .env.example .env && php artisan key:generate`
3. `php artisan jwt:secret`
4. `php artisan app:init --all`
5. `npm install && npm run dev`

Run tests
- Ensure a `.env.testing` file exists (example at `.env.testing.example`).
- `php artisan test`
- The test environment uses a `.env.testing` configuration file. An example is provided at `.env.testing.example`.

Structure (short)
- `app/`: application code
- `routes/`: routes (api.php)
- `config/`: configuration files
- `tests/`: automated tests

Endpoints
- API: [http://localhost:8080/api](http://localhost:8080/api)
- API Documentation: [http://localhost:8080/request-docs](http://localhost:8080/request-docs)

Contributing
- Open issues or pull requests. For questions, use the repository tracker.

This README is a short summary. For full details, check config files and root commands.

### Port already in use (8080)

```bash
# Change the port in docker-compose.yml
# Find the nginx service and change "8080:80" to "8081:80" or another port
docker-compose up -d --build
```

### Container exits immediately

```bash
# Check logs for errors
docker-compose logs backserver

# Rebuild the image
docker-compose up -d --build --force-recreate
```


---

## Architecture

The project follows a modular architecture. Features are organized under `app/Modules/` (for example `Auth`, `User`).

Each module is generally self-contained and includes its own:
- Controllers
- DTOs
- Repositories
- Requests
- Resources
- Routes and tests

Run tests
- Ensure a `.env.testing` file exists (example at `.env.testing.example`).
- Use `php artisan test` to run the test suite.
- The initialization command `php artisan app:init --all` creates a testing database named `testing` used by the test environment.
