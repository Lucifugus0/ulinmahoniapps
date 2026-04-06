# CLAUDE.md — Ulin Mahoni


## Rules
1. Do not change any existing API, to keep existing mobile app, frontend and backend compability.
2. Do not rename column in db table, to keep existing mobile app, frontend, api and backend compability.
3. Use mark down comment to explain what the code do in every changes of the code.
4. After every new module or bug fix, update `whatsnew.md` in the project root. Entries must be categorized under **Backend**, **Frontend (Web Portal)**, or **Mobile App**, and placed in either the **New Modules** or **Bug Fixes** table for that category. Each entry must include the date (YYYY-MM-DD), page/screen name, and a short description of the change.


## Project Overview

Ulin Mahoni is a property rental booking platform for Indonesian kos (dorm-style housing) targeting Gen Z renters. It is a **monorepo** with three applications:

| Directory | Stack | Purpose |
|-----------|-------|---------|
| `Backend/` | Laravel 11, PHP 8.2+, Vite, TailwindCSS 4, Alpine.js | Admin dashboard & internal API |
| `Frontend API/web-laravel-ulinmahoni/` | Laravel 9, PHP 8.0+, Vite, TailwindCSS 3, Alpine.js | Public web portal & main REST API |
| `Mobile App/` | Flutter/Dart 3.7.2+, Riverpod, GoRouter, Dio | Cross-platform mobile app (iOS/Android) |

## Build & Run Commands

### Backend (Admin Dashboard)

```bash
composer install && npm install
php artisan migrate && php artisan db:seed
npm run dev          # Dev assets (Vite watch)
php artisan serve --port=8000    # Dev server at :8000
npm run build        # Production assets
```

### Frontend API (Web Portal)

```bash
composer install && npm install
php artisan migrate && php artisan db:seed
npm run dev          # Dev assets
php artisan serve --port=8001    # Dev server at :8001
npm run build        # Production assets
docker-compose up    # Docker: PHP 8.3 + MySQL 8.0
```

### Mobile App

```bash
flutter pub get
flutter run              # Run on connected device
flutter build apk        # Android release
flutter build ios        # iOS release
flutter analyze          # Lint check
flutter test             # Run tests
```

## Local Development

### Prerequisites
- PHP 8.2+ (note: PHP 8.5 works but shows deprecation warnings — suppressed via `error_reporting(E_ALL & ~E_DEPRECATED)` in `public/index.php`)
- MySQL 9.x or MariaDB
- Node.js 24+, npm 11+
- Composer 2.x

### Local Database Setup
Both Backend and Frontend API share the same local database (matching production where both use one DB):
- **Database:** `iosadmin_ulinmahoni-demo` (local MySQL, user: root, no password)

The schema is imported from production (migrations alone won't work due to missing tables and dependencies).

To refresh local DB from production:
```bash
ssh umadminpanel@ulinmahoni.com "mysqldump -u umadminpanel_root -p'DigitaLL24\$\$' umadminpanel_ulinmahoni-db-live --no-data" | \
  (echo "SET FOREIGN_KEY_CHECKS=0;" && cat) | mysql -u root "iosadmin_ulinmahoni-demo"
```

### Frontend API → Backend Image Loading
The Frontend API loads property/room images from the Backend via `ADMIN_URL` env variable. Locally:
- `APP_URL=http://127.0.0.1:8001` (Frontend API)
- `ADMIN_URL=http://127.0.0.1:8000` (Backend, serves images at `/storage/...`)
- Backend must have `php artisan storage:link` run

### SSH Access to Production
```bash
ssh umadminpanel@ulinmahoni.com
# MySQL on server (remote MySQL port blocked, use SSH):
ssh umadminpanel@ulinmahoni.com "mysql -u umadminpanel_root -p'DigitaLL24\$\$' umadminpanel_ulinmahoni-db-live -e 'QUERY'"
```
Note: Direct MySQL connection fails due to MariaDB 10.11 (server) vs MySQL 9.6 (local) protocol mismatch.

## Testing

- **PHP (both Laravel apps):** `php artisan test` (PHPUnit). Tests in `tests/Unit/` and `tests/Feature/`.
- **Flutter:** `flutter test`

## Architecture & Key Patterns

### Database Naming

- `m_*` tables = master/main entities (e.g., `m_properties`, `m_rooms`)
- `t_*` tables = transaction/temporary entities
- Foreign keys: `{table}_id`
- Migrations prefixed with timestamps

### API Response Format

```json
{
  "status": "success|error",
  "message": "Human-readable message",
  "data": {},
  "errors": {}
}
```

### Authentication

- Laravel: Jetstream + Sanctum (Bearer tokens)
- Flutter: Google Sign-In, Apple Sign-In, biometric, email/password
- Rate limits: 5 login attempts/min/IP, 60 API requests/min/token

### Key Integrations

- **Payments:** DOKU gateway (virtual accounts for BRI/BNI/BCA/Mandiri/etc., QRIS)
- **Real-time:** Pusher (chat WebSockets), Firebase Cloud Messaging (push notifications)
- **Push Notifications:** Firebase Cloud Messaging via Frontend API (see Push Notifications section below)
- **Storage:** Local or AWS S3 (configured via `.env`)
- **OAuth:** Google (via Laravel Socialite)

### Daily Room Pricing
All 5 pricing categories are required at daily room creation: `weekday_price`, `weekend_price`, `holiday_price`, `high_season_price`, `low_season_price`. Priority: high_season > low_season > holiday > weekend > weekday. Manual price overrides are preserved. Service: `RoomPriceGeneratorService.php`.

### Booking Constraints

| | Max Check-in Date | Max Stay Duration |
|---|---|---|
| **Daily** | 90 days from today | 60 days |
| **Monthly** | 14 days from today | 12 months |

These limits are enforced in both Frontend API (web datepicker) and Mobile App (date picker + duration stepper).

### Booking Flow

pending → waiting → paid → completed/cancelled/expired

### Booking Table (`t_booking`) Key Columns
- `check_in_at` — actual check-in timestamp (set by admin)
- `checked_in_by` — admin user ID who performed check-in
- `check_out_at` — actual check-out timestamp (set by admin)
- `checked_out_by` — admin user ID who performed check-out
- `status` — 1 = active, 0 = inactive (after checkout or room change)
- The scheduled check-in/check-out dates are in `t_transactions.check_in` / `t_transactions.check_out`

### Backend Sidebar Structure
Flat group hierarchy with 3 standalone items and 7 collapsible groups:
- **Standalone:** Dashboard, Room Availability, Chat
- **Groups:** Bookings (incl. Door Lock, Parking), Finance, Promo, Reports, Masters (incl. Customers, Users), App Management (Maintenance Mode, Access Management), Miscellaneous

### Booking Pages (Backend Admin)
- **All Bookings** — shows all bookings, hides expired by default (checkbox to show)
- **Pending** — bookings awaiting payment
- **Confirmed Bookings** — paid bookings ready for check-in
- **Checked-In** — currently occupied bookings (no action column)
- **Check-out Hari Ini** — bookings due today or overdue, with checkout modal; checkbox to show all checked-in
- **Completed** — shares the allbookings_table partial
- All pages default to 25 items per page
- User name displays `first_name + last_name` from user model (falls back to `transaction.user_name`)
- Checkout modal is a shared partial: `checkin/partials/checkout_modal_button.blade.php` (requires `checkOutModal` Alpine component registered on the page)

## Push Notifications (Firebase Cloud Messaging)

Push notifications are implemented in the **Frontend API** (not Backend). The Backend has no FCM code.

### Frontend API (Server-Side)
- **Service:** `app/Services/FirebaseNotificationService.php` — sends via FCM v1 API with OAuth2 JWT auth
- **Config:** `config/firebase.php` — reads `FIREBASE_PROJECT_ID` and `FIREBASE_CREDENTIALS` env vars; credentials stored at `storage/app/firebase-credentials.json`
- **Device Token Model:** `app/Models/DeviceToken.php` — table `device_tokens` (`user_id`, `token`, `device_type`, `device_name`, `is_active`)
- **Migration:** `database/migrations/2026_03_25_152806_create_device_tokens_table.php`

### API Endpoints
| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/api/v1/device-token` | Register/update device FCM token |
| DELETE | `/api/v1/device-token` | Remove device token (on logout) |
| POST | `/api/v1/push-notification/send` | Manual send with type-based templates |

### Notification Types (predefined templates)
`booking_created`, `check_in`, `booking_renewed`, `payment_received`, `booking_expired`, `custom`

### Auto-Triggered Notifications
- New booking created → guest + admins
- Check-in → guest + admins
- Booking renewal → guest + admins
- Booking expiry (via `ExpireBooking` job) → guest + admins
- DOKU payment received (VA & QR callbacks) → guest + admins

### Mobile App (Client-Side)
- **FCM Service:** `lib/core/services/fcm_service.dart` — token management, foreground/background/terminated message handling
- **Local Notifications:** `lib/core/services/local_notification_service.dart` — displays notifications when app is in foreground
- **FCM Repository:** `lib/core/data/repositories/fcm_repository.dart` — backend token sync endpoints (currently commented out)
- **Known Issue:** Mobile app's `FCMRepository` points to `/api/users/fcm-token` but Frontend API expects `/api/v1/device-token` — endpoint mismatch needs fixing
- **Known Issue:** Token sync code in `FCMService` is commented out (TODO) — needs uncommenting and endpoint correction

### Dependencies
- **Frontend API:** No composer package — uses raw HTTP to FCM v1 API with JWT
- **Mobile App:** `firebase_core: ^3.8.1`, `firebase_messaging: ^15.1.5`, `flutter_local_notifications: ^17.2.3`

## Maintenance Mode

- **Backend toggle:** `MaintenanceModeController` at `pages/settings/maintenance-mode.blade.php` — Alpine.js toggle switch that updates `global_title` table (`maintenance_mode` = '0' or '1')
- **Frontend API middleware:** `CheckMaintenanceMode` — checks `global_title` table; returns JSON 503 for API requests, redirects to `/maintenance` for web
- **Maintenance page:** `resources/views/maintenance.blade.php` — standalone bilingual page (EN + ID) with Tailwind CDN
- **Mobile support:** Health-check API includes `maintenance_mode` flag

## Code Style

- **PHP:** Laravel preset (StyleCI), 4-space indent, UTF-8, LF line endings
- **JavaScript:** Prettier with `tabWidth: 4`, `useTabs: true`
- **Dart/Flutter:** `package:flutter_lints` strict rules, enforced via `flutter analyze`
- **YAML:** 2-space indent

## Environment Setup

Each app has a `.env.example` — copy to `.env` and configure. Never commit `.env` files. Key variables include database credentials, DOKU payment keys, Firebase config, and AWS S3 settings.

## Multi-Language (i18n)

All three apps support Indonesian (default), English, and Chinese (Simplified):
- **Backend:** `lang/en/ui.php` and `lang/id/ui.php` (~1360 keys). Language switcher in header on every page. Uses `SetLocale` middleware (session-based for guests, user DB column for authenticated). Default locale: `id` (set in `config/app.php`).
- **Frontend API:** Laravel language files in `/lang/` (id, en, zh) with language switcher in navbar. Booking-specific translations in `lang/{locale}/booking.php`.
- **Mobile App:** Flutter localization via `/lib/l10n/` with ARB files (`app_id.arb`, `app_en.arb`, `app_zh.arb`).

### Adding new translation keys
1. Add key to both `Backend/lang/en/ui.php` and `Backend/lang/id/ui.php`
2. Use `{{ __('ui.key_name') }}` in Blade templates
3. For JS template literals, use `{{ __('ui.key_name') }}` inside Blade `<script>` tags

## Dark Mode

### Backend (Admin Dashboard)
- Toggle in header (sun/moon icon), persisted in `localStorage('dark-mode')`
- Tailwind v4 `dark:` prefix classes **do not work** unless the class is already in the compiled CSS bundle
- Use `html.dark .class` selectors with `!important` in `<style>` blocks within blade templates or in `resources/css/app.css`
- **For element-specific dark overrides that CSS specificity can't solve**, add a custom CSS class (e.g., `.search-filter-container`) to the element, then use a high-specificity selector in `resources/css/app.css` like `html.dark div.search-filter-container.bg-white { ... !important; }`. The `html.dark .bg-white` global rule will override lower-specificity selectors, so the custom class selector must include the original Tailwind class to win.
- Alpine.js `x-bind:style` does NOT work for elements outside an `x-data` scope — use CSS approach instead.
- Global dark mode overrides are in `resources/css/app.css` (body bg, table bg, text colors, borders, inputs including `input[type="tel"]`, etc.)
- Shared dark mode badge styles for booking pages: `pages/bookings/partials/dark-badge-styles.blade.php`
- Modal headers (check-in, check-out) use `.checkin-modal-header` / `.checkout-modal-header` classes with dark overrides in `app.css`
- Dashboard-specific dark styles use custom CSS classes (`.card-header-green`, `.card-indigo`, etc.) defined in `<style>` block at top of `dashboard.blade.php`
- Header opacity: 35% for both light and dark modes (backdrop-blur with 0.35 opacity on ::before pseudo-element)
- After CSS changes, always run `npm run build` to rebuild assets

### Frontend API (Web Portal)
- Toggle in header (sun/moon icon), persisted in `localStorage('dark-mode')`, applied via `document.documentElement.classList.add('dark')`
- Tailwind v3 CSS is compiled — `dark:` prefix classes **only work if already present** in the compiled CSS bundle
- Available dark classes in compiled CSS: `dark:bg-gray-900`, `dark:bg-gray-700`, `dark:border-gray-600`, `dark:text-gray-400`, `dark:text-gray-300`
- For new dark mode styles, use `html.dark .class` selectors with `!important` in `<style>` blocks within blade templates
- Room detail pages (`pages/room/show.blade.php`, `pages/room/en/show.blade.php`) load Tailwind v2 CDN — still use `html.dark` overrides for reliability

### Mobile App
- Theme managed by `ThemeProvider` (Riverpod StateNotifier), persisted in SharedPreferences, dark mode is default
- Central theme factory: `lib/core/theme/app_theme.dart` (Material 3, Apple liquid glass design)
- Glass design tokens: `lib/core/theme/glass_theme.dart` (glassmorphism blur, surface colors, borders)
- Color constants: `lib/core/constants/appcolor_constants.dart` (light/dark variants matching Frontend's `#111827` dark bg)
- Dark mode applied across all 47+ widget files (login, register, navigation, dialogs, payment, booking, profile)

## Staging Environment

> Both staging apps point to `~/repositories/Ulin-Mahoni-New/` on the server (not `public-staging-*` directories).

| | Value |
|---|---|
| **Frontend URL** | staging.ulinmahoni.com |
| **Backend URL** | staging-admin.ulinmahoni.com |
| **API URL** | https://staging.ulinmahoni.com/api/v1 |
| **Database** | umadminpanel_ulinmahoni-db-staging |

### Server Repository Paths (Staging)
- **Backend:** `~/repositories/Ulin-Mahoni-New/Backend/`
- **Frontend API:** `~/repositories/Ulin-Mahoni-New/Frontend API/web-laravel-ulinmahoni/`

### Deploying to Staging
Files must be deployed to the `Ulin-Mahoni-New` paths above. After deploying blade templates:
```bash
# Frontend API
ssh umadminpanel@ulinmahoni.com "find ~/repositories/Ulin-Mahoni-New/'Frontend API'/web-laravel-ulinmahoni/storage/framework/views/ -name '*.php' -delete"

# Backend
ssh umadminpanel@ulinmahoni.com "find ~/repositories/Ulin-Mahoni-New/Backend/storage/framework/views/ -name '*.php' -delete"
```
Note: `php artisan view:clear` may fail on staging due to PHP version mismatch (server has PHP 8.3, Backend requires 8.4). Use the `find -delete` approach instead.

### Staging Deployment Notes
- **No npm on staging server** — build assets locally with `npm run build`, then upload `public/build/` via scp:
  ```bash
  scp -r Backend/public/build/ umadminpanel@ulinmahoni.com:~/repositories/Ulin-Mahoni-New/Backend/public/build/
  ```
- **php artisan migrate fails** on staging (PHP 8.3 vs 8.4 required) — run SQL directly:
  ```bash
  ssh umadminpanel@ulinmahoni.com "mysql -u umadminpanel_root -p'DigitaLL24\$\$' umadminpanel_ulinmahoni-db-staging -e 'SQL_HERE'"
  ```
- Staging may have local uncommitted changes — use `git stash` before `git pull`

## Production Server Credentials

> **WARNING:** Do not push this file to a public repository.

| | Value |
|---|---|
| **Frontend URL** | ulinmahoni.com |
| **Backend URL** | admin.ulinmahoni.com |
| **cPanel User** | umadminpanel |
| **cPanel Password** | DigitaLL24$$ |

### Production Database

| | Value |
|---|---|
| **Database** | umadminpanel_ulinmahoni-db-live |
| **DB User** | umadminpanel_root |
| **DB Password** | DigitaLL24$$ |

### Server Repository Paths (Production)
- **Backend:** `~/repositories/web-laravel-admin-um/`
- **Frontend API:** `~/repositories/web-laravel-ulinmahoni/`
