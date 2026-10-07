# RED BAG

**A BAG OF LIFE** — a digital blood donation and emergency blood-help platform.

## Current foundation

- Laravel 13
- PHP 8.3+
- Vite + Tailwind CSS
- MySQL-ready data model
- Redis-ready queues/cache configuration
- Bengali-first responsive homepage
- Donor registration flow
- Blood request flow
- Core donor and blood request models/migrations

## Local setup

1. Install PHP 8.3+, Composer, Node.js, MySQL and Redis.
2. Copy `.env.example` to `.env`.
3. Set MySQL credentials and create the `redbag` database.
4. Run `composer install`.
5. Run `php artisan key:generate`.
6. Run `php artisan migrate`.
7. Run `npm install && npm run dev`.
8. Run `php artisan serve`.

## Product roadmap

### Phase 1 — Life-saving core
Donor OTP verification, donor profile, availability, blood request verification, emergency escalation, donor matching, notifications, request tracking and admin operations.

### Phase 2 — Community
Digital donor card, donation history, recognition, certificates, campaigns, referral, campus and corporate programs.

### Phase 3 — Ecosystem
Hospital portal/API, advanced analytics, AI request assistant and native mobile apps.

> RED BAG is a connection and coordination platform. Medical eligibility and clinical blood-bank decisions remain with qualified medical professionals/authorized services.
