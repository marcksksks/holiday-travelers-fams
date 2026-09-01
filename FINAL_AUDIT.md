# FAMS Final Technical Audit

## Status

Final hardening pass completed against the supplied Laravel FAMS project.

### Fixed in this pass

- Disabled public self-registration for an administrative system.
- Added API JSON enforcement for deactivated accounts and forced-password-change accounts.
- Added separate appointment view/manage permissions.
- Added appointment conflict checking with facility row locking and controlled status transitions.
- Removed client-controlled appointment status from the appointment request payload.
- Added reservation date and facility-capacity validation.
- Added transactional reservation submission and decision handling with facility locking.
- Hardened reservation cancellation against invalid terminal states.
- Added API authorization for visitor, document, legal, contract, and appointment reads/writes.
- Added document confidentiality checks before signed downloads.
- Added cleanup of replaced legal/contract/document files where applicable.
- Added legal/document date-order validation.
- Hardened contract workflow state transitions.
- Prevented removal/deactivation of the last active system administrator.
- Removed the invalid email-verification notification call from administrator-created users.
- Replaced the Google Calendar refresh-token-as-access-token bug with a real OAuth refresh-token exchange.
- Added configurable Google Calendar ID, HTTP timeout/retry, and calendar endpoint handling.
- Added a Render persistent disk for private uploaded documents.
- Added database indexes and the missing appointment-to-visitor foreign key.
- Added regression tests covering authentication state, RBAC, reservations, appointments, user-admin protection, and contract workflow.

## Validation

- PHP syntax scan: **PASS (0 errors)**
- YAML parse validation: **PASS**
- JavaScript static syntax check: project-dependent; full Vite build requires dependency installation.
- Full PHPUnit execution: **not available in this environment because Composer is not installed**.
- Full production frontend build: **not executed because dependency installation is unavailable/timed out in the execution environment**.

## Production setup requirements

1. Set a real HTTPS `APP_URL` in Render.
2. Configure PostgreSQL credentials through Render's database connection.
3. Configure the Render persistent disk or migrate the documents disk to S3-compatible object storage for a multi-instance deployment.
4. If Google Calendar is used, configure:
   - `GOOGLE_CALENDAR_CLIENT_ID`
   - `GOOGLE_CALENDAR_CLIENT_SECRET`
   - `GOOGLE_CALENDAR_REFRESH_TOKEN`
   - `GOOGLE_CALENDAR_ID`
   - `GOOGLE_CALENDAR_TIMEZONE`
5. Run migrations with `php artisan migrate --force`.
6. Run `php artisan test` after Composer dependencies are installed.
7. Run `npm install` and `npm run build` before production deployment if assets are being built outside Docker.

## Important architecture note

Reservation and appointment overlap protection uses application/database transactions and row locking. This is stronger than a simple pre-check, but if the system later scales horizontally, database-level scheduling constraints or a dedicated booking model may be desirable for even stronger invariants.
