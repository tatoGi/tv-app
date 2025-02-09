# TV Authentication API

A Laravel-based API service that provides authentication for TV devices using a code-based authentication flow.

## Features

- User registration and authentication
- TV device authentication using one-time codes
- Rate limiting for security
- Comprehensive logging
- Token-based authentication using Laravel Passport

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/tv-auth-api.git
cd tv-auth-api
```

2. Install dependencies:
```bash
composer install
```

3. Set up environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database in `.env` and run migrations:
```bash
php artisan migrate
```

5. Install Passport:
```bash
php artisan passport:install
```

## API Documentation

For detailed API documentation, please see [docs/api.md](docs/api.md).

The API provides endpoints for:
- User Authentication (register, login, logout)
- TV Device Authentication (generate code, activate code, poll for token)

## Security

- Rate limiting on all authentication endpoints
- Token expiration
- Secure code generation
- Request logging for audit trails

## License

This project is open-sourced software licensed under the [MIT license](LICENSE.md).
