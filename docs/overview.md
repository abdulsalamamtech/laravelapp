# Application Overview

A standard Laravel 13 starter kit designed for rapid API development, built on PHP 8.4 with SQLite as the default database. The application follows the Controller-Service-Repository (CSR) pattern for separation of concerns and provides a Filament 5 admin panel alongside a versioned REST API.

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Project Setup](#project-setup)
- [Application Libraries](#application-libraries)
- [Codebase Structure](#codebase-structure)
- [Domain Flows](#domain-flows)
- [Database Schema](#database-schema)
- [Filament Admin Panel](#filament-admin-panel)
- [Testing](#testing)
- [CI/CD & Deployment](#cicd--deployment)
- [Scheduling & Queues](#scheduling--queues)
- [Development CLI Commands](#development-cli-commands)

---

## Tech Stack

| Layer          | Technology                       |
| -------------- | -------------------------------- |
| PHP            | 8.4                              |
| Framework      | Laravel 13                       |
| Admin panel    | Filament 5                       |
| Auth           | Laravel Sanctum 4 + OAuth 5.31   |
| Database       | SQLite (default), MySQL supported |
| Queue driver   | Database (default)               |
| Frontend bundler| Vite 7 + Tailwind CSS 4         |
| Test runner    | Pest 4                           |
| API docs       | Scramble (OpenAPI)               |

---

## Project Setup

### Requirements

- PHP 8.2+ (8.4 recommended)
- Composer 2
- Node.js 22+ and npm

### Quick Start

```sh
git clone <repo-url> && cd laravelapp
composer setup          # installs deps, copies .env, generates key, migrates, builds assets
```

Or step-by-step:

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=DefaultRoleSeeder
php artisan db:seed --class=AdminSeeder
npm install && npm run build
```

### Running in Development

```sh
composer dev
```

This runs four processes concurrently: `php artisan serve`, `php artisan queue:listen`, `php artisan pail`, and `npm run dev`.

### Environment Variables

Key `.env` entries:

| Variable              | Purpose                                      |
| --------------------- | -------------------------------------------- |
| `APP_URL`             | Application base URL                         |
| `FRONTEND_URL`        | Frontend app URL for CORS and mail links     |
| `DB_CONNECTION`       | Database driver (`sqlite` / `mysql`)         |
| `MAIL_MAILER`         | Mail transport (`log`, `smtp`, etc.)         |
| `SANCTUM_STATEFUL_DOMAINS` | Comma-separated SPA domains for stateful auth |
| `MEDIA_DISK`          | Spatie Media Library disk (`public`)         |
| `PULSE_ENABLED`       | Toggle Laravel Pulse metrics                 |
| `SENTRY_LARAVEL_DSN`  | Sentry error monitoring DSN                 |
| `NIGHTWATCH_ENABLED`  | Toggle Laravel Nightwatch agent              |
| `SCORING_MODE`        | `legacy` or `config` (business health scoring) |

---

## Application Libraries

### Admin Panel & Plugins

| Package | Version | Purpose |
| ------- | ------- | ------- |
| `filament/filament` | 5.8.1 | Core admin panel framework |
| `bezhansalleh/filament-shield` | 4.3.1 | Role & permission management via UI |
| `alizharb/filament-activity-log` | 1.5.0 | Timeline-based activity log for Filament |
| `achyutn/filament-log-viewer` | 2.5.0 | Browse application log files in Filament |
| `achyutn/filament-storage-monitor` | 1.4.2 | Monitor server disk usage (local, public, backups) |
| `croustibat/filament-jobs-monitor` | 4.6.0 | Monitor queue jobs across all drivers |
| `dotswan/filament-laravel-pulse` | 2.2.2 | Laravel Pulse dashboard widget for Filament |
| `filament/spatie-laravel-media-library-plugin` | 5.8.1 | Media Library integration for Filament forms/tables |
| `shuvroroy/filament-spatie-laravel-backup` | 3.4.0 | Backup management UI in Filament |

**Installation (pre-installed via `composer setup`):** Registered in `app/Providers/Filament/AdministratorPanelProvider.php`. Each plugin's navigation and authorization is configured in that provider's `plugins()` array.

### Laravel Ecosystem

| Package | Version | Purpose |
| ------- | ------- | ------- |
| `laravel/framework` | 13.31.0 | Core framework |
| `laravel/sanctum` | 4.3.3 | API token authentication |
| `laravel/socialite` | 5.31.0 | OAuth social login (Google, GitHub) |
| `laravel/pulse` | 1.8.1 | Application metrics dashboard |
| `laravel/nightwatch` | 1.30.0 | Production telemetry and observability |
| `laravel/pail` | 1.2.7 | Real-time log streaming |
| `laravel/tinker` | 3.0.2 | Interactive REPL |

### Spatie Packages

| Package | Version | Purpose |
| ------- | ------- | ------- |
| `spatie/laravel-permission` | 8.3.0 | Roles and permissions (backing `HasRoles` trait) |
| `spatie/laravel-activitylog` | 4.12.3 | Audit trail for Eloquent models (uses `LogsActivity` trait) |
| `spatie/laravel-backup` | 10.3.2 | Database and file backups with cleanup strategies |
| `spatie/laravel-data` | 4.23.0 | Data transfer objects |
| `spatie/laravel-medialibrary` | 11.23.7 | File/media attachments with collections and conversions |
| `spatie/laravel-query-builder` | 7.3.5 | Filter, sort, and include API query parameters |

### Tooling & Utilities

| Package | Version | Purpose |
| ------- | ------- | ------- |
| `dedoc/scramble` | 0.13.43 | Auto-generated OpenAPI docs at `/docs/api` |
| `maatwebsite/excel` | 3.1.70 | Import/export Excel/CSV (backed by PHPSpreadsheet) |
| `rappasoft/laravel-authentication-log` | 6.1.1 | Login and device tracking with IP geolocation |
| `sentry/sentry-laravel` | 4.27.0 | Error monitoring and performance tracing |
| `torann/geoip` | 3.0.10 | IP-to-country geolocation for auth logging |

### Development Tools

| Package | Version | Purpose |
| ------- | ------- | ------- |
| `pestphp/pest` | 4.7.8 | Test framework (extends PHPUnit) |
| `phpstan/phpstan` | 2.2.14 | Static analysis (level 5, see `phpstan.neon`) |
| `rector/rector` | 2.6.7 | Automated code quality refactoring |
| `laravel/pint` | 1.32.1 | Code style fixer (Laravel preset) |
| `fruitcake/laravel-debugbar` | 4.4.3 | Debug bar for local development |
| `laravel/boost` | 2.8.1 | Laravel Boost MCP server for AI coding agents |
| `nunomaduro/collision` | 8.9.5 | Pretty CLI error reporting |
| `mockery/mockery` | 1.6.15 | Test doubles and stubs |
| `fakerphp/faker` | 1.24.1 | Fake data generation for tests and seeders |
| `laravel/sail` | 1.67.0 | Docker development environment |

### Frontend

| Package | Version | Purpose |
| ------- | ------- | ------- |
| `vite` | 7.3.0 | Asset bundler |
| `tailwindcss` | 4.1.18 | Utility-first CSS framework |
| `axios` | 1.13.2 | HTTP client |
| `concurrently` | 9.2.1 | Parallel script runner for `composer dev` |

---

## Codebase Structure

```
laravelapp/
├── app/
│   ├── Console/Commands/         # Custom Artisan commands
│   │   ├── QualityCheck.php      # app:check — run lint, analyse, rector, tests
│   │   └── SendReverificationEmails.php
│   ├── Data/V1/                  # (reserved for data transfer objects)
│   ├── Enums/
│   │   ├── AppRole.php           # super_admin, admin, user, client
│   │   ├── EmailChangeStatus.php # pending, scheduled, applied, cancelled, expired
│   │   ├── EmailChangeCancelledReason.php
│   │   ├── EmailChangeWindow.php # 24-hour window constant
│   │   ├── Http.php              # HTTP status code enum
│   │   └── OtpTokenType.php      # account-verification, reset-password, 2fa, email-change-*
│   ├── Exceptions/
│   │   └── ProFeatureAccessException.php
│   ├── Filament/
│   │   ├── Exports/              # (reserved for Filament exports)
│   │   ├── Pages/
│   │   │   ├── AdminDashboard.php
│   │   │   ├── AuthenticationLogs.php
│   │   │   ├── PulsePage.php
│   │   │   └── Reports.php
│   │   ├── Resources/
│   │   │   ├── AllowedOrigins/AllowedOriginResource.php
│   │   │   ├── ChatMessages/ChatMessageResource.php
│   │   │   ├── Media/MediaResource.php
│   │   │   ├── Roles/RoleResource.php
│   │   │   ├── Users/UserResource.php
│   │   │   └── Waitlists/WaitlistResource.php
│   │   └── Widgets/
│   │       ├── AccountWidget.php
│   │       ├── StatsOverview.php
│   │       └── TransactionOverviewStatsWidget.php
│   ├── Helpers/
│   │   └── ApiResponse.php       # Standardized JSON response helper
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── MailPreviewController.php
│   │   │   ├── SocialiteController.php
│   │   │   └── V1/
│   │   │       ├── AuthController.php
│   │   │       ├── EmailChangeController.php
│   │   │       ├── Admin/AdminContactController.php
│   │   │       └── Quest/
│   │   │           ├── ChatMessageController.php
│   │   │           ├── ContactController.php
│   │   │           └── WaitlistController.php
│   │   ├── Requests/
│   │   │   ├── StoreContactRequest.php
│   │   │   ├── UpdateContactRequest.php
│   │   │   └── Quest/
│   │   └── Resources/Company/CompanyReportResource.php
│   ├── Imports/
│   │   └── FinancialDataImport.php
│   ├── Jobs/
│   │   └── ProcessReverificationEmailJob.php
│   ├── Libraries/                # (reserved for reusable domain libraries)
│   ├── Mail/
│   │   ├── AlertUserMail.php
│   │   ├── CompanyWelcomeMail.php
│   │   ├── ContactMail.php
│   │   ├── EmailChangeAcceptanceMail.php
│   │   ├── EmailChangeCompletedMail.php
│   │   ├── EmailChangeConfirmationMail.php
│   │   ├── EmailChangeRequestMail.php
│   │   ├── EmailChangeWindowMail.php
│   │   ├── ExceptionMail.php
│   │   ├── ForgetPasswordMail.php
│   │   ├── ReverifyEmailMail.php
│   │   ├── TwoFactorAuthMail.php
│   │   ├── VerifyAccountMail.php
│   │   └── WaitlistMail.php
│   ├── Models/
│   │   ├── ChatMessage.php
│   │   ├── Contact.php
│   │   ├── OtpToken.php
│   │   ├── PendingEmailChange.php
│   │   ├── User.php
│   │   ├── Waitlist.php
│   │   ├── Custom/
│   │   │   ├── AllowedOrigin.php
│   │   │   └── Media.php
│   │   └── System/
│   │       ├── Permission.php
│   │       ├── PersonalAccessToken.php
│   │       └── Role.php
│   ├── Notifications/
│   │   └── AlertUser.php
│   ├── Policies/                 # Authorization policies
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── Filament/AdministratorPanelProvider.php
│   │   └── NightwatchServiceProvider.php
│   └── Services/
│       ├── EmailChangeService.php
│       ├── MailService.php
│       ├── OtpTokenService.php
│       ├── V1/                   # (reserved for V1 API services)
│       └── Custom/
│           ├── FlutterwaveBillingService.php
│           ├── PaystackBillingService.php
│           └── Interfaces/BillingServiceInterface.php
├── config/
│   ├── activitylog.php
│   ├── app.php
│   ├── auth.php
│   ├── authentication-log.php
│   ├── backup.php
│   ├── cors.php
│   ├── database.php
│   ├── filament-activity-log.php
│   ├── filament-shield.php
│   ├── geoip.php
│   ├── media-library.php
│   ├── permission.php
│   ├── pulse.php
│   ├── scoring.php               # Business health scoring config
│   ├── scramble.php              # OpenAPI documentation config
│   ├── sentry.php
│   └── sanctum.php
├── database/
│   ├── factories/
│   │   ├── AllowedOriginFactory.php
│   │   ├── PendingEmailChangeFactory.php
│   │   └── UserFactory.php
│   ├── migrations/               # 28 migration files
│   └── seeders/
│       ├── AdminSeeder.php
│       ├── DatabaseSeeder.php
│       └── DefaultRoleSeeder.php
├── docs/
│   ├── db.md
│   ├── flow.md
│   └── overview.md               # This file
├── phpstan.neon                  # PHPStan level 5 config (paths: app/)
├── phpstan-baseline.neon         # Baseline of pre-existing PHPStan errors (253) — new errors only fail
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── filament/
│       ├── mail/                 # Blade mail templates
│       └── welcome.blade.php
├── routes/
│   ├── api.php
│   ├── console.php
│   ├── web.php
│   └── v1/
│       ├── auth.php
│       ├── admin.php
│       ├── services.php
│       └── quest.php
└── tests/
    ├── Feature/
    │   ├── Api/V1/
    │   │   ├── AuthTest.php
    │   │   └── EmailChangeTest.php
    │   ├── V1/EmailChangeTest.php
    │   ├── QualityCheckCommandTest.php
    │   └── ...
    └── Unit/
        ├── ExampleTest.php
        └── NightwatchIngestBindingTest.php
```

---

## Domain Flows

### Authentication Flow

```
POST /api/v1/register  →  User created → OTP sent via email
POST /api/v1/verify-account (email + otp)  →  Email marked verified → Welcome mail sent → Sanctum token returned
```

**Login:**

```
POST /api/v1/login (email + password)
  ├─ 2FA enabled  →  OTP sent → POST /api/v1/verify-two-factor-otp (email + otp) → token returned
  └─ 2FA disabled →  Token returned directly (24h expiry)
```

**Password reset:**

```
POST /api/v1/forgot-password (email)  →  OTP sent to email
POST /api/v1/confirm-password (email + otp + new password)  →  Password updated
```

**Protected endpoints:** All routes under `auth:sanctum` middleware. Token-based auth via `Authorization: Bearer {token}`.

### Email Change Workflow

A dual-inbox verification workflow with a 24-hour window:

```
POST /api/v1/email/change (pending_email)
  → Codes emailed to BOTH current and new addresses

Current inbox confirms:  POST /api/v1/email/change/confirm-old (pending_email + otp)
New inbox confirms:      POST /api/v1/email/change/confirm-new (pending_email + otp)
  → Status becomes SCHEDULED; acceptance + report codes sent to both inboxes

New inbox accepts:       POST /api/v1/email/change/accept (pending_email + otp)
  → effective_at window passes → email is applied automatically

Current inbox cancels:   POST /api/v1/email/change/cancel (pending_email + otp)
Current inbox reports:   POST /api/v1/email/change/report (pending_email + otp)
  → Change cancelled; all sessions revoked
```

**Status lifecycle:** `pending` → `scheduled` → `applied` | `cancelled` | `expired`

### Quest Endpoints (Public, rate-limited)

| Method | Endpoint | Purpose |
| ------ | -------- | ------- |
| POST | `/api/v1/contacts` | Submit a contact message |
| POST | `/api/v1/waitlists` | Join the waitlist |
| POST | `/api/v1/chat-messages` | Submit a chat message |

### Admin Endpoints

Protected by `auth:sanctum` + `role:admin|super_admin` middleware:

| Method | Endpoint | Purpose |
| ------ | -------- | ------- |
| GET | `/api/v1/admin/contacts` | List contact messages |
| GET | `/api/v1/admin/contacts/{contact}` | View a contact message |
| PUT | `/api/v1/admin/contacts/{contact}` | Update a contact message |
| DELETE | `/api/v1/admin/contacts/{contact}` | Delete a contact message |

---

## Database Schema

All primary models use **UUIDv7** primary keys (auto-generated on creation). Key tables:

| Table | Purpose |
| ----- | ------- |
| `users` | User accounts (name, email, password, phone, app_role, 2FA, address fields, socialite IDs) |
| `otp_tokens` | OTP tokens for verification, 2FA, password reset, email change (hashed, with expiry + failed_attempts) |
| `pending_email_changes` | Email change requests with full audit trail and state machine |
| `contacts` | Contact form submissions (soft-deleted, activity logged) |
| `waitlists` | Waitlist entries (soft-deleted, activity logged) |
| `chat_messages` | Chat messages (soft-deleted) |
| `allowed_origins` | Dynamic CORS origins (cached, invalidated on save/delete) |
| `media` | Spatie Media Library file records |
| `roles` / `permissions` / `model_has_roles` / `model_has_permissions` | Spatie Permission tables |
| `personal_access_tokens` | Sanctum API tokens |
| `activity_log` | Spatie Activity Log audit records |
| `authentication_log` | Login/device tracking (IP, user agent, location) |
| `imports` / `exports` / `failed_import_rows` | Filament import/export records |
| `filament_jobs_monitor_jobs` | Filament Jobs Monitor queue job records |

---

## Filament Admin Panel

Accessed at `/administrator` with login required.

### Panel Configuration

Configured in `app/Providers/Filament/AdministratorPanelProvider.php`:
- **ID:** `administrator`
- **Path:** `administrator`
- **Sidebar:** Collapsible on desktop
- **Database notifications:** Enabled

### Resources

| Resource | Model | Description |
| -------- | ----- | ----------- |
| `UsersResource` | `User` | User management with Filament form/table schemas |
| `RolesResource` | `Role` | Role management (core roles `super_admin`/`admin` protected from edit/delete) |
| `MediaResource` | `Media` | Browse and manage uploaded media files |
| `WaitlistsResource` | `Waitlist` | Manage waitlist entries |
| `ChatMessagesResource` | `ChatMessage` | View chat messages |
| `AllowedOriginsResource` | `AllowedOrigin` | Manage CORS allowed origins (invalidates cache on save) |

### Pages

| Page | Purpose |
| ---- | ------- |
| `AdminDashboard` | Custom dashboard replacing the default |
| `PulsePage` | Laravel Pulse metrics dashboard |
| `AuthenticationLogs` | Login and device tracking logs |
| `Reports` | Reporting page |

### Plugins

| Plugin | Authorization | Notes |
| ------ | ------------- | ----- |
| FilamentShield | All authenticated | Role/permission management UI |
| ActivityLogPlugin | All authenticated | Navigation group: System |
| FilamentLogViewer | `super_admin` only | Browse log files; Navigation group: System |
| FilamentSpatieLaravelBackup | `super_admin` only | Backup management; Navigation group: Settings |
| FilamentJobsMonitor | `super_admin` only | Queue job monitoring; Navigation group: System |
| FilamentStorageMonitor | `super_admin` only | Disk usage for local, public, web root, backups, data partition |

---

## Testing

### Framework

Tests use **Pest 4** (extends PHPUnit). The base `TestCase` uses `RefreshDatabase`. The `phpunit.xml` configures an in-memory SQLite database for tests.

### Running Tests

```sh
# Full suite
php artisan test --compact

# Or via Pest directly
vendor/bin/pest

# Single file
vendor/bin/pest tests/Feature/QualityCheckCommandTest.php

# Filter by test name
vendor/bin/pest --filter="runs the full quality suite"
```

### Test Structure

```
tests/
├── Feature/
│   ├── Api/V1/
│   │   ├── AuthTest.php
│   │   └── EmailChangeTest.php
│   ├── V1/EmailChangeTest.php
│   ├── QualityCheckCommandTest.php
│   ├── SocialiteControllerTest.php
│   ├── MailPreviewTest.php
│   ├── ExampleTest.php
│   ├── Jobs/
│   ├── Libraries/
│   └── Services/
├── Unit/
│   ├── ExampleTest.php
│   └── NightwatchIngestBindingTest.php
├── Pest.php
└── TestCase.php
```

---

## CI/CD & Deployment

### GitHub Actions Workflows

| Workflow | Branch | Purpose |
| -------- | ------ | ------- |
| `tests.yml` | develop, staging, main | Run Pest test suite (PHP 8.4, Node 22) |
| `lint.yml` | develop, staging, main | Run Pint (format) + Rector (dry-run) |
| `staging-vps.yml` | staging | Deploy to VPS via SSH (git pull, migrate, seed, optimize) |
| `staging-hostinger-deploy.yml` | staging | Deploy to Hostinger shared hosting (tests, build assets, rsync, migrate) |
| `prod-vps.yml` | main | Production VPS deployment |
| `prod-hostinger-deploy.yml` | main | Production Hostinger deployment |

### Deploy Steps (VPS)

```
git pull → composer install → npm install → npm run build
→ migrate --force → seed → optimize:clear → optimize
→ shield:generate --all → queue:restart
```

### Cron

The `cron.sh` script runs `php artisan schedule:run` and is configured via server cron.

---

## Scheduling & Queues

### Scheduled Tasks (routes/console.php)

| Schedule | Command / Closure | Purpose |
| -------- | ----------------- | ------- |
| Every minute | `queue:work --stop-when-empty --timeout=300 --tries=3 --backoff=60` | Process queued jobs (run in background, without overlapping) |
| Every minute | `nightwatch:agent` | Collect telemetry (if `NIGHTWATCH_ENABLED=true`) |
| Hourly | `app:send-reverification-emails` | Send re-verification emails to unverified users (chunks of 30) |
| Daily | OTP token cleanup closure | Remove expired `otp_tokens` rows |
| Hourly | Email change housekeeping closure | Apply due email changes and expire stale ones |

### Queue Config

Default driver: `database`. Jobs use `ProcessReverificationEmailJob` for bulk re-verification emails. The scheduler wraps the queue worker to handle jobs continuously.

---

## Development CLI Commands

### Quality Suite

```sh
php artisan app:check           # Full suite: Pint (lint) → PHPStan → Rector (dry-run) → Pest tests
php artisan app:check --fix     # Apply fixes: Pint (format) + Rector (process) + tests
php artisan app:check --skip=analyse --skip=rector  # Run only lint + tests
```

**Stages (default, read-only mode):**

| Stage | Tool | Behavior |
| ----- | ---- | -------- |
| lint | Pint | `--test` (reports style issues without modifying files) |
| analyse | PHPStan | Level 5 static analysis of `app/` per `phpstan.neon`, ignoring errors recorded in `phpstan-baseline.neon` |
| rector | Rector | `process --dry-run` (reports code quality changes without applying) |
| tests | Pest | Full test suite |

With `--fix`, Pint runs without `--test` and Rector runs without `--dry-run`, modifying files in place.

**Notes:**

- Each subprocess runs with a **300-second timeout** (`->timeout(300)` in `runStage()`). The full Pest suite takes ~75–100 s on this machine, which exceeds Symfony Process's default 60 s — without this, `app:check` would time out on the tests stage.
- Rector is invoked with `--no-progress-bar` only. It has **no `--no-progress` or `--no-interaction` options**, and passing either breaks the command.
- Rector dry-run exits with **code 2 when changes are proposed** (read-only gate). In `--fix` mode Rector applies those changes, so the same report produces a PASS.
- PHPStan runs against **plain PHPStan 2** (Larastan is not installed), so Laravel magic methods, facades, and dynamic model properties produce false positives. The first run reported **253 errors**, which were captured into `phpstan-baseline.neon` via `--generate-baseline`. The `analyse` stage therefore **passes on existing code but fails on any new error** — future fixes should remove baseline entries rather than add more.
- The command returns **exit code 0 only when every stage passes**, exit code 1 otherwise (including when a dry-run stage proposes changes).

### Composer Script Aliases

```sh
composer test              # Clear config cache + run full Pest suite
composer format            # Fix style in changed files only (pint --dirty)
composer format:all        # Fix style across entire codebase (pint)
composer analyse           # PHPStan level 5 static analysis (phpstan neon)
composer check             # Alias for php artisan app:check
composer fix               # Alias for php artisan app:check --fix
composer dev               # Concurrent: serve + queue:listen + pail + vite
```

> **Note:** `composer format` uses `pint --dirty`, which only works inside a **Git repository**. In a non-git directory it fails with "The [--dirty] option is only available when using Git" — use `composer format:all` (or `vendor/bin/pint <file>`) instead.

### Verification Status (static analysis gate)

The full suite is green as of the last run:

| Check | Result |
| ----- | ------ |
| `composer validate` | `./composer.json is valid` |
| `vendor/bin/pint --test` | Passes on all modified files |
| `vendor/bin/phpstan analyse` | Passes (level 5 + baseline of 253 pre-existing errors) |
| `vendor/bin/rector process --dry-run` | No changes proposed |
| `vendor/bin/pest` | **69 passed (250 assertions)** |
| `php artisan app:check` (full pipeline) | `[PASS]` lint, analyse, rector, tests — exit 0 |

### Other Useful Commands

```sh
# Testing
php artisan test --compact
php artisan test --filter=AuthTest

# Code quality
vendor/bin/pint --dirty                # Format changed files (Git repo only)
vendor/bin/pint                        # Format entire codebase
vendor/bin/rector process --dry-run    # Preview Rector refactoring (read-only)
vendor/bin/rector process              # Apply Rector refactoring
vendor/bin/phpstan analyse             # Level 5 (per phpstan.neon, with baseline)
vendor/bin/phpstan analyse --generate-baseline  # Refresh phpstan-baseline.neon

# Filament
php artisan shield:generate --all --option=policies_and_permissions --panel=administrator
php artisan shield:generate --all --option=policies --panel=administrator

# Database
php artisan migrate --force
php artisan db:seed --class=DefaultRoleSeeder
php artisan db:seed --class=AdminSeeder

# Optimization
php artisan optimize:clear
php artisan optimize
php artisan config:cache
php artisan route:cache

# Monitoring
php artisan pail                        # Stream logs in real time
php artisan pulse:check                 # Check Pulse worker status

# Backups
php artisan backup:run
php artisan backup:clean
php artisan backup:monitor

# API docs
php artisan scramble:clear               # Clear Scramble cache
# Live docs: https://domain.com/docs/api

# Queue
php artisan queue:work --stop-when-empty --timeout=300 --tries=3 --backoff=60
php artisan queue:restart
```
