# Support Ticket API v1.0.0

## Highlights
- Role-based Support Ticket workflow (admin/agent/customer)
- Tickets with priorities, categories, statuses, soft-deletes, restore/force delete
- Replies with attachments and notifications
- Auth via Sanctum, policies across tickets/replies
- Filters, search, pagination + caching on ticket listing
- Swagger docs at `/api/documentation`
- CI: PHPUnit + Pint GitHub Actions

## Deploy checklist
- `php artisan migrate --force && php artisan storage:link`
- Seed base data: `php artisan db:seed`
- Start queue worker: `php artisan queue:work`
- Generate docs: `php artisan l5-swagger:generate`
- Tag release: `git tag v1.0.0 && git push --tags`
