# FAMS — Laravel Conversion

This is a full conversion of the original React/Vite + Base44 **Facility &
Administrative Management System (FAMS)** into **Laravel Blade + Tailwind
CSS**, **PHP/Laravel**, **PostgreSQL**, **Laravel Sanctum**, and a **REST
API**, deployable via **GitHub Actions → Render**.

## 1. What the original app was

The uploaded project was a Base44-generated app: a React/Vite frontend with
no traditional backend of its own — data lived in Base44-hosted "entities"
(`base44/entities/*.jsonc`) and business logic lived in Base44 serverless
functions (`base44/functions/*/entry.ts`). There were 12 entities, 6 roles,
and 9 workflow functions. This conversion re-implements every one of them
natively in Laravel.

## 2. Entity → Laravel mapping

| Base44 Entity | Laravel table / model |
|---|---|
| `User` | `users` / `App\Models\User` (extends Sanctum-ready `Authenticatable`) |
| `Facility` | `facilities` / `App\Models\Facility` |
| `Reservation` | `reservations` / `App\Models\Reservation` |
| `Appointment` | `appointments` / `App\Models\Appointment` |
| `Visitor` | `visitors` / `App\Models\Visitor` |
| `ArchiveDocument` | `archive_documents` / `App\Models\ArchiveDocument` |
| `Contract` | `contracts` / `App\Models\Contract` |
| `LegalRecord` | `legal_records` / `App\Models\LegalRecord` |
| `RetentionPolicy` | `retention_policies` / `App\Models\RetentionPolicy` |
| `RecordRetention` | `record_retentions` / `App\Models\RecordRetention` |
| `AuditLog` | `audit_logs` / `App\Models\AuditLog` |
| `AppNotification` | `app_notifications` / `App\Models\AppNotification` |

## 3. Server function → Laravel service mapping

| Base44 function | Laravel replacement |
|---|---|
| `submitReservation` | `App\Services\ReservationService::submit()` |
| `decideReservation` | `App\Services\ReservationService::decide()` |
| `contractWorkflow` | `App\Services\ContractWorkflowService` (submitForReview/legalReview/decide/renew) |
| `visitorCheckFlow` | `App\Services\VisitorCheckService` (checkIn/checkOut/decline) |
| `documentAccess` | `App\Services\DocumentAccessService` (confidentiality-gated, signed download URLs) |
| `createUser` | `App\Services\UserManagementService` |
| `syncAppointmentToCalendar` | `App\Services\CalendarSyncService` (Google Calendar REST call) |
| `aiVisitorAssist` | `App\Services\AiAssistService` (pluggable provider; safe rule-based fallback) |
| `listStaff` | `GET /api/users` (sys_admin) or `App\Services\NotificationService::usersByRole()` internally |

Every service call is paired with an `AuditLog` entry and, where the
original notified users, an `AppNotification` row — matching the original
behaviour exactly (see `src/lib/rbac.js` → `App\Support\Rbac`).

## 4. RBAC

`App\Support\Rbac` is a line-for-line port of the original `rbac.js`:
`NAV` (which routes each of the 6 roles can see) and `PERMISSIONS` (fine
grained abilities). `AppServiceProvider` turns every `PERMISSIONS` entry
into a Laravel `Gate`, so `@can('manageContracts')` in Blade and
`$user->can('manageContracts')` in controllers "just work". Route groups
in `routes/web.php` use a small `role:` middleware for coarse page-level
gating, matching the original `NAV` map.

Roles: `employee`, `receptionist`, `admin_officer`, `manager`,
`legal_officer`, `sys_admin`.

## 5. Project structure

```
app/
├── Http/
│   ├── Controllers/        Web (Blade) controllers
│   ├── Controllers/Api/    REST API controllers (routes/api.php)
│   ├── Requests/           Form Request validation for every entity
│   ├── Resources/          API Resources (JSON shaping)
│   └── Middleware/         EnsureActiveAccount, ForcePasswordChange, EnsureUserHasRole
├── Models/                 12 Eloquent models
├── Services/               Ported business logic (see table above)
└── Support/Rbac.php        NAV + PERMISSIONS matrix
database/
├── migrations/             12 domain tables + auth/cache/queue/sanctum tables
└── seeders/DatabaseSeeder.php
resources/views/            Blade + Tailwind UI (one folder per module)
routes/
├── web.php                 Server-rendered app (Blade)
└── api.php                 REST API (Sanctum)
docker/, Dockerfile, render.yaml   Render deployment
.github/workflows/ci.yml    CI: install → migrate → test → lint
```

## 6. Running locally

Requirements: PHP 8.2+, Composer, Node 20+, PostgreSQL 14+.

```bash
cp .env.example .env
composer install
npm install

php artisan key:generate

# create the database first, e.g.:
#   createdb fams
# then set DB_* in .env to match

php artisan migrate --seed
npm run build      # or `npm run dev` for hot-reload while developing

php artisan serve
```

Visit `http://localhost:8000`. Seeded accounts (password: `password`,
all `force_password_change = false`):

| Email | Role |
|---|---|
| sysadmin@fams.local | System Administrator |
| manager@fams.local | General Manager |
| admin.officer@fams.local | Administrative Officer |
| legal@fams.local | Legal Officer |
| reception@fams.local | Receptionist |
| employee@fams.local | Employee / Staff |

**Change these passwords (or remove the seeder accounts) before any
production deployment.**

## 7. REST API

All endpoints are under `/api/*`, authenticated with **Laravel Sanctum**
(works for both SPA-style cookie auth and bearer-token clients). Example:

```
GET    /api/me
GET    /api/facilities
POST   /api/facilities
GET    /api/reservations
POST   /api/reservations
POST   /api/reservations/{id}/decide
GET    /api/appointments
GET    /api/visitors
POST   /api/visitors/{id}/check-in
POST   /api/visitors/{id}/check-out
GET    /api/documents
POST   /api/documents/{id}/request-link
GET    /api/legal-records
GET    /api/contracts
POST   /api/contracts/{id}/submit-review
POST   /api/contracts/{id}/legal-review
POST   /api/contracts/{id}/decide
GET    /api/users            (sys_admin only)
```

To call the API from a bearer-token client, issue a personal access token
(e.g. via `php artisan tinker` → `$user->createToken('name')->plainTextToken`)
and send `Authorization: Bearer <token>`.

## 8. Environment variables

See `.env.example` for the full list. Notable ones:

- `DB_*` — PostgreSQL connection.
- `SANCTUM_STATEFUL_DOMAINS` / `CORS_ALLOWED_ORIGINS` — set to your real
  frontend domain(s) in production.
- `GOOGLE_CALENDAR_*` — optional; without these, calendar sync returns a
  clear validation error instead of failing silently.
- `AI_ASSIST_*` — optional; without these, AI visitor-assist returns a
  clearly-labelled rule-based fallback instead of failing.

No secrets are hardcoded anywhere in the codebase — everything sensitive
is read from `.env` / the hosting platform's environment variables.

## 9. GitHub Actions

`.github/workflows/ci.yml` runs on every push/PR to `main`: installs PHP
and Node dependencies, spins up a real PostgreSQL 16 service container,
builds frontend assets, runs migrations against it, runs `php artisan
test`, and lints with Pint.

## 10. Render deployment

`render.yaml` defines a Blueprint with:
- a Docker web service (built from the included multi-stage `Dockerfile`,
  which compiles Composer deps, builds Tailwind/Vite assets, then serves
  via nginx + PHP-FPM behind Supervisor), and
- a managed PostgreSQL database, wired to the web service purely through
  environment variables (`fromDatabase` references — no hardcoded
  credentials).

On deploy, `docker/start.sh` caches config/routes/views and runs
`php artisan migrate --force` automatically before starting the server.

To deploy: push this repo to GitHub, then in Render choose **New →
Blueprint** and point it at the repo — `render.yaml` does the rest. Set
`SANCTUM_STATEFUL_DOMAINS` to your final Render URL once it's assigned.

## 11. What's implemented vs. what to extend

Fully implemented: all 12 entities/tables/models, all RBAC rules, all 9
business workflows (with audit logging + notifications), Blade UI for
every module in the original nav (dashboard, facilities, reservations,
appointments, visitor desk, records archive with confidentiality-gated
signed downloads, legal records, contracts with the full
draft → legal review → approval → renew lifecycle, retention register,
reports, audit trail, staff accounts), the matching REST API, CI, and
Render deployment config.

Reasonable next steps if you continue building on this:
- Wire real Google OAuth2 refresh-token exchange in `CalendarSyncService`
  (currently expects a valid long-lived token directly in `.env`).
- Connect `AiAssistService` to your preferred LLM provider by filling in
  `AI_ASSIST_*`; it degrades gracefully without one.
- Add feature tests per module (the CI pipeline already runs `php artisan
  test`, there just aren't test files included yet).
- Add pagination/sorting controls and search boxes to the index views if
  your record volumes grow past a page or two.
