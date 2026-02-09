# Laravel REST API Application

<p align="center">
    <a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo"></a>
</p>

## 📋 Overview

A backend application built with **Laravel 12** that provides a complete REST API with JWT authentication, user management, and extensible modules. It uses Docker for streamlined deployment and development, Vite for asset compilation, and PHPUnit for testing.

### ✨ Key Features

- **Complete REST API** with well-structured routes
- **JWT Authentication** with `php-open-source-saver/jwt-auth`
- **Modular Architecture** with Auth and User modules
- **DTOs and Repositories** for separation of concerns
- **Automatic API Documentation** with `laravel-request-docs`
- **Dockerized** with compose for rapid development
- **Vite** for asset compilation with Tailwind CSS
- **PHPUnit** for automated testing
- **Queue System** for background task processing

---

## 🛠️ Requirements

- **PHP** ^8.4 (with PHP-FPM)
- **MariaDB** 10.11
- **Composer** 2
- **Docker & Docker Compose** (optional, recommended)

---

## 🚀 Installation & Setup

### Option 1: With Docker (Recommended)

```bash
# Clone the repository
git clone <your-repository>
cd project

# Copy environment file
cp .env.example .env

# Start containers
docker-compose up -d --build

# Wait for MariaDB to be ready, then initialize application
docker-compose exec backserver php artisan app:init --all

# Generate JWT secret
docker-compose exec backserver php artisan jwt:secret
```

### Option 2: Local Installation

```bash
# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret

# Initialize database
php artisan app:init --all

# Start development server
php artisan serve
```

---

## 📁 Project Structure

```
app/
├── Common/              # Shared code
│   ├── Controllers/     # Base controllers
│   ├── DTOs/           # Data Transfer Objects
│   ├── Repositories/   # Repository pattern
│   ├── Requests/       # Form Requests
│   └── Responses/      # Response formatters
├── Http/
│   ├── Controllers/    # HTTP Controllers
│   ├── Middleware/     # Custom middleware
│   └── Resources/      # API Resources
├── Models/             # Eloquent Models
├── Modules/            # Feature modules
│   ├── Auth/          # Authentication
│   └── User/          # User management
└── Providers/          # Service Providers

database/
├── factories/          # Model Factories for testing
├── migrations/         # Database migrations
└── seeders/           # Database seeds

routes/
├── api.php            # API routes
├── web.php            # Web routes
└── console.php        # Artisan commands

tests/
├── Feature/           # Integration tests
└── Unit/             # Unit tests

config/                # Configuration files
resources/            # Views and Vite assets
storage/             # Logs, uploads, cache
```

---

## 🔐 JWT Authentication

### Generate JWT Secret

```bash
php artisan jwt:secret
```

The token will be automatically generated and saved in `.env` as `JWT_SECRET`.

### Using Auth Middleware

```php
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
```

### Request Headers

```
Authorization: Bearer {token}
```

---

## 🗄️ Database

### Run Migrations

```bash
php artisan migrate
```

### Seed Database

```bash
php artisan db:seed
php artisan db:seed --class=UserFakeSeeder
```

### Create New Models

```bash
php artisan make:model ModelName -m -f -c -s
```

---

## 🔧 Application Initialization

The `app:init` command initializes the entire application with the following options:

```bash
# Initialize everything (migrations, seeders, test database)
php artisan app:init --all

# Only initialize database
php artisan app:init --initdb

# Initialize database with fake data
php artisan app:init --initdb --seed

# Only seed fake data
php artisan app:init --seed
```

**What it does:**

- Clears configuration and application cache
- Clears compiled files cache
- Restarts queue workers
- Runs database migrations with refresh (if `--initdb` or `--all`)
- Seeds required base data
- Seeds fake user data (if `--seed` or `--all` is specified)
- Creates testing database for PHPUnit tests

---

## 📝 Testing

### Run Tests

```bash
# All tests
php artisan test

# Feature tests
php artisan test tests/Feature

# Unit tests
php artisan test tests/Unit

# With coverage report
php artisan test --coverage
```

### Create Tests

```bash
php artisan make:test ExampleTest
php artisan make:test Api/UserApiTest --feature
```

---

## 🔄 Development

### Start Development Environment

Run all services simultaneously (server, queue, logs, vite):

```bash
composer run dev
```

Or manually:

```bash
# Terminal 1: Laravel Server
php artisan serve

# Terminal 2: Queue Worker
php artisan queue:listen

# Terminal 3: Real-time Logs
php artisan pail

# Terminal 4: Vite (Assets)
npm run dev
```

### Compile Assets

```bash
# Development
npm run dev

# Production
npm run build
```

---

## 📚 API Documentation

Automatic API documentation is available at:

```
http://localhost:8080/request-docs
```

Generated automatically using `laravel-request-docs`.

---

## 🏗️ Module Structure

### Auth Module

Handles authentication and authorization:

- User login / logout
- User registration
- JWT token refresh
- Password management

### User Module

Manages user information:

- User CRUD operations
- User profiles
- Role management
- Input validation

---

## ⚙️ Environment Variables

```env
# Database
DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=user_db
DB_PASSWORD=password_db

# JWT
JWT_SECRET=your_secret_key
JWT_ALGORITHM=HS256
JWT_TTL=60

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@example.com

# Cache & Queue
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

---

## 🐳 Docker

### Services & Versions

- **PHP-FPM** 8.4 - Laravel 12 application server with Composer 2
- **MariaDB** 10.11 - Database server (Port 3306)
- **Nginx** Alpine - Web server (Port 8080)

### Useful Commands

```bash
# View logs from all containers
docker-compose logs -f

# View logs from a specific service
docker-compose logs -f backserver

# Execute artisan command in container
docker-compose exec backserver php artisan tinker

# Execute shell in container
docker-compose exec backserver /bin/bash

# Restart all services
docker-compose restart

# Stop all containers
docker-compose down

# Rebuild and start
docker-compose up -d --build

# Initialize the application after containers start
docker-compose exec backserver php artisan app:init --all
```

### Access Points

- **API** - http://localhost:8080
- **API Docs** - http://localhost:8080/request-docs
- **Database** - localhost:3306 (MariaDB)

---

## 🐛 Troubleshooting

### Migration table not found

```bash
php artisan migrate:install
php artisan app:init --initdb
```

### Database connection refused in Docker

```bash
# Wait for database to be ready and reinitialize
docker-compose exec backserver php artisan app:init --all
```

### JWT token expired

Verify `JWT_TTL` is correctly configured in `.env` (value in minutes, default: 60).

### Permission denied errors on storage

```bash
docker-compose exec backserver chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker-compose exec backserver chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
```

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

## 📦 Composer Scripts

```bash
composer setup        # Complete setup
composer dev          # Development with all services
composer test         # Run tests
```

---

## 📄 License

MIT License. See LICENSE file for details.

---

## 👥 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📞 Support

For bug reports or feature requests, please open an issue in the repository.

---

**Last Updated:** February 2026

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
