# Support Ticket System API

A RESTful API built with Laravel for managing support tickets, replies, attachments, roles, and notifications.

## Stack
- Laravel 12 API-only, Sanctum auth
- Roles: admin, agent, customer
- Tickets with priorities, categories, statuses, replies, attachments
- Policies + role-based access
- Notifications (mail + database), queued
- Filters/search/pagination, caching
- Swagger docs via L5 Swagger
- CI: PHPUnit + Pint workflows

## Quick start
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link   # for attachments
php artisan serve
```

Seeded test accounts (password `password`):
- admin@example.com
- agent@example.com
- customer@example.com

## Useful scripts
- `php artisan l5-swagger:generate` — build OpenAPI docs at `/api/documentation`
- `php artisan test` — run feature tests
- `./vendor/bin/pint` — format code

## Deployment notes
- Queue connection uses database; run `php artisan queue:work`
- FILESYSTEM_DISK defaults to `local`; adjust if using S3 and update `Storage::url` config.
