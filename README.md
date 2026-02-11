# Space-Channel

Social network for a miniature game community: battle reports, mini galleries, tournaments, army lists and unit discussions.

## Tech
- Symfony **7.4 (LTS)** + Twig (server-rendered)
- SQLite (early/small community friendly)
- Bootstrap 5 (via CDN in `templates/base.html.twig`)

## Requirements
- PHP 8.2+
- Composer

## Local setup

Install dependencies (this project currently ignores a local `ext-redis` version constraint, since Redis is not required):

```bash
composer install --ignore-platform-req=ext-redis
```

Run the dev server:

```bash
symfony server:start
# or
php -S localhost:8000 -t public
```

Then open `http://localhost:8000`.

## Database
SQLite DB file is `var/data.db` (configured via `DATABASE_URL` in `.env`).

When you start adding entities:

```bash
php bin/console make:entity
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## Legacy note
The initial repo README was kept as `README.legacy.md`.

