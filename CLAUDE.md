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

- **Payments:** DOKU gateway (virtual accounts for BRI/BNI/BCA/Mandiri/etc., QRIS). **BRI Manual is deprecated** as of 2026-05-08 — code references remain only to render historical rows; no new flows create BRI Manual transactions.
- **Real-time:** Pusher (chat WebSockets), Firebase Cloud Messaging (push notifications)
- **Push Notifications:** Firebase Cloud Messaging via Frontend API (see Push Notifications section below)
- **Storage:** Local or AWS S3 (configured via `.env`)
- **OAuth:** Google (via Laravel Socialite)

### DOKU SNAP-BI Virtual Account format
SNAP-BI VA endpoints require `partnerServiceId` to be numeric, **max 8 characters**, left-padded with spaces if shorter. DGPC env vars (`DOKU_BNI_DGPC`, `DOKU_BRI_DGPC`, etc., in the Frontend API `.env`) feed this directly via `Api/BookingController::dokuGenerateVA`. **A 9+ digit env value silently fails** with response code `4002701 Invalid Field Format {partnerServiceId}` because `str_pad` in the controller only pads up, not down — over-length values get sent as-is. Validate any new bank's DGPC against DOKU's documented example for that bank before deploying.

### Daily Room Pricing
All 5 pricing categories are required at daily room creation: `weekday_price`, `weekend_price`, `holiday_price`, `high_season_price`, `low_season_price`. Priority: high_season > low_season > holiday > weekend > weekday. Manual price overrides are preserved. Service: `RoomPriceGeneratorService.php`.

### Booking Constraints

**New bookings** — caps on the *check-in* date selected at booking time.

| | Max Check-in Date | Max Stay Duration |
|---|---|---|
| **Daily** | 90 days from today | 60 days |
| **Monthly** | 14 days from today | 12 months |

**Renewals** — caps on the *new check-out* date (since check-in is locked to the previous booking's check-out).

| | Max New Check-out | Cumulative Cap |
|---|---|---|
| **Daily** | today + 60 days | — |
| **Monthly** | (no cap from today) | today + 15 months (chain) |

**Renewal window guards** — apply to both daily and monthly:
- Renewal modal opens only if `daysUntilCheckOut ≤ 90` ("too early to renew" block)
- **Same-day cutoff** on check-out day itself: **12:00 WIB for daily** (new guest arrives after noon checkout), **21:00 WIB for monthly**

All caps are enforced server-side in `Api/BookingController::checkAvailability` and `renewBooking`, and mirrored client-side in the web modal (`bookings/index.blade.php`) and mobile dialog (`renew_booking_dialog.dart` + cutoff check in `mybookingdetails_page.dart`).

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
- **Groups:** Bookings (incl. Door Lock, Parking), Finance, Promo, Reports, Masters (incl. Customers, Users), App Management (Access Rights only — Maintenance Mode is super-admin-only and lives outside the sidebar), Miscellaneous

The sidebar is driven by `m_sidebar_items` + `PermissionsTableSeeder`. **Access Rights** (formerly "Master Role" at `/settings/master-role-management`) is the per-role permission editor; each leaf sidebar item now has its own dedicated `view_*` permission so toggling one item never grants/revokes another. After editing the seeder, run `php artisan db:seed --class=SidebarItemsTableSeeder` (and `PermissionsTableSeeder` if permissions changed) on each environment to sync.

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

## Parking System

### Storage: one row per paid period (post-2026-05-08)
`t_parking` is a per-period audit trail. Every paid renewal/payment that includes parking **INSERTs a new row** with its own `start_rent` / `end_rent`; previous rows stay as history. "Currently active" = `status = 1 AND deleted_at IS NULL` (and conventionally also `end_rent >= CURDATE()`, but the daily cron keeps `status` in sync). The 2026-05-08 design pivot from single-row-update-in-place is preserved as `t_parking_single_line_old` on production (89 rows, rollback path). The `(property_id, vehicle_plate)` unique index was **dropped** — same plate now legitimately has multiple rows over time.

### Three write paths (all INSERT new row, never update existing)
- **Frontend web bundled** — `Frontend API/.../BookingController::updatePaymentMethod` (renewal + initial branches both insert)
- **Mobile API bundled** — `Frontend API/.../Api/BookingController::updatePaymentMethod` (same logic; computes `start_rent`/`end_rent` from booking dates)
- **Backend Finance > Parking Entry standalone** — `Backend/.../ParkingPaymentController::store`. Creates a `t_parking_fee_transaction` row alongside, whose `parking_id` is repointed to the new t_parking `idrec`.

The Backend Properties → Parking page (`/properties/parking`) is **read-only** — no Add button, no Edit/Delete actions. All new parking entries flow exclusively through Finance → Parking Entry. The page does have a property combobox (HQ/HO only; site users auto-scoped) and a "Show Expired" toggle.

### Daily expiry cron at 00:00
`php artisan parking:deactivate-expired` flips `status = 1 → 0` on (a) rows where `end_rent < CURDATE()`, and (b) rows where `end_rent IS NULL` (orphan / legacy / failed write). Idempotent; supports `--dry-run`. Registered in `Backend/Console/Kernel.php` AND wired as direct system crontab entries on each environment (Laravel scheduler is not running on this server):
- Staging: `0 0 * * * cd ~/repositories/Ulin-Mahoni-New/Backend && /usr/local/bin/php artisan parking:deactivate-expired >> ~/logs/staging_parking_deactivate.log 2>&1`
- Production: same pattern against `~/repositories/web-laravel-admin-um/`

### `m_parking_fee.quota_used` is legacy/drifted — do not display
The persisted "currently used" counter drifted upward for months because increments fired on every renewal while decrements were missed on many close paths. **Don't surface `quota_used` in any new UI**. Live capacity for any chart/table:
```sql
SELECT COUNT(*) FROM t_parking
WHERE property_id = ? AND parking_type = ?
  AND status = 1 AND deleted_at IS NULL;
```
Capacity (denominator) still comes from `m_parking_fee.capacity` — that column is admin-set and trustworthy. The `/properties/parking` chart and `/properties/property-fees` page both follow this pattern (the latter had its `quota_used` counter removed on 2026-05-08).

### Server-side parking-entry guard
`ParkingPaymentController::store` rejects when the matching `m_parking_fee` row is missing OR `capacity <= 0`. Pre-2026-05-07 a `capacity = 0` row meant "unlimited"; that interpretation is gone — admins must set an explicit non-zero slot count before any parking can be sold.

### Invoice number resolution (display)
- `Parking::invoice_display` accessor → latest paid `t_parking_fee_transaction.invoice_id` → fall back to `t_transactions.invoice_number` → null
- `Parking::invoice_source` accessor → `'addon'` (standalone parking payment) / `'bundled'` (paid with room) / null
The Parking Management table renders the invoice number on row 1 and a colored chip on row 2 ("Booking + Parking" blue / "Add on Parking" purple).

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

## Brand Colors (Ulin Mahoni palette)

Use these hex values for any new brand-aligned UI (buttons, accents, icons, badges). Tailwind utility colors (e.g. `green-600`, `emerald-500`) are fine for generic UI; reach for brand hexes when the surface needs to read as Ulin Mahoni rather than generic.

| Color | Hex | Use |
|---|---|---|
| **UM Green** | `#0F513D` | Primary brand — logo, primary buttons, headings, accent borders |
| **UM Maroon Red** | `#800000` | Secondary brand — alerts, highlights, "Penuh"/full-state pills, contrast accents |

Pair with neutrals (`#f8f7f4` cream/light bg, `#111827` dark mode bg) for grounding.

### Frontend (Web Portal) theme-aware accent
Light mode `--accent: #0F513D` (UM Green), dark mode `--accent: #a83333` (saturated UM Maroon variant — pure `#800000` was too dark on `#111827`). Defined in `Frontend API/.../resources/views/components/homepage/styles.blade.php`. Use `var(--accent)` / `var(--accent-hover)` for any new brand surfaces; `.btn-um-themed` is the canonical primary-button class. Tailwind `teal-*` utility classes are remapped to the accent variable with `!important` to beat the Tailwind Play CDN runtime CSS.

## Promo Banner (`/promo-banners`)

Data lives in `m_promo_banners`. Two JSON-cast columns drive the homepage detail modal:

- **`how_to_claim`** — array of step objects: `[{title: string, desc: string}, …]`. Min 2, max 5 entries, both fields required per entry (Backend validates).
- **`terms_conditions`** — array of strings: `[string, …]`. Min 1 entry.

**Backward compatibility** — pre-2026-05-05 banners stored `how_to_claim` as a flat string array `[string, …]`. Both apps normalize-on-read: legacy entries are auto-promoted to `{title: "Langkah N", desc: <original-string>}` so old data keeps rendering without a migration. Normalization lives in `Backend/app/Http/Controllers/PromoBannerController::normalizeHowToClaim()` (called from `show()`) and inline in `Frontend API/.../app/Http/Controllers/homepage/HomeController::index()`. **Never** assume a banner stores either shape exclusively — always run it through the normalizer.

**Banner image sizes** — frontend banner is **1920 × 620 px** (was 1911 × 372 px pre-2026-05). Mobile banner unchanged.

**Cara Klaim animation is disabled** — all step circles render with the `.active` class statically (lit gradient + scaled). The `startStepsAnimation()` cycling logic in `promos.blade.php` is left in place but no longer invoked.

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
- **Backend has a split webroot layout** — the Laravel app lives in `~/repositories/.../Backend/` but the actual served document root is a separate directory: `~/public_html/admin.ulinmahoni.com/` (prod) or `~/public_html/staging-admin.ulinmahoni.com/` (staging). Laravel reads the Vite `manifest.json` from the **repo's** `public/build/`, but the browser fetches asset files from the **webroot's** `build/`. When deploying CSS/JS, you must copy `public/build/manifest.json` AND the referenced hashed asset files from the repo's `public/build/assets/` into the webroot's `build/assets/` — otherwise users get 404s on the new filenames. Old hashed files can stay (they'll just go unused). Frontend API has the same split: repo `~/repositories/web-laravel-ulinmahoni/` vs webroot `~/public_html/web.ulinmahoni.com/` / `~/public_html/staging.ulinmahoni.com/`.
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

Production was migrated to the monorepo on **2026-05-13**. Both prod repos now sparse-checkout from this monorepo and track `monorepo/production`. The OUTER directory names are kept for backwards compatibility with cron jobs / scripts, but the Laravel app now lives one level deeper.

- **Backend repo root:** `~/repositories/web-laravel-admin-um/` → Laravel app at `~/repositories/web-laravel-admin-um/Backend/`
- **Frontend API repo root:** `~/repositories/web-laravel-ulinmahoni/` → Laravel app at `~/repositories/web-laravel-ulinmahoni/Frontend API/web-laravel-ulinmahoni/`

Both repos have `core.sparseCheckout = true` with cone-mode paths set (`Backend` and `Frontend API/web-laravel-ulinmahoni` respectively). Top-level files from the monorepo root (e.g. `CLAUDE.md`, `whatsnew.md`, root-level `.md`s) sit alongside the subdir but are harmless.

### Monorepo deploy-key auth (production only)

Production authenticates to the private monorepo via an SSH deploy key (read-only):
- **Key location:** `~/.ssh/id_ed25519_ulinmahoni_monorepo` (+ `.pub`)
- **SSH alias:** `github-ulinmahoni-monorepo` (configured in `~/.ssh/config`)
- **Remote URL on both repos:** `git@github-ulinmahoni-monorepo:trisnotjhin/Ulin-Mahoni.git` (as remote name `monorepo`)
- **GitHub config:** Repo Settings → Deploy keys → "ulinmahoni-prod (read-only)"

Original `origin` remotes (pointing at `farhans29/web-laravel-admin-um` and `farhans29/web-laravel-ulinmahoni`) are kept on the repos for historical reference but no longer drive deploys.

### Deploying to Production

```bash
# 1. Pull latest production branch
ssh umadminpanel@ulinmahoni.com "cd ~/repositories/web-laravel-admin-um && git pull monorepo production"
ssh umadminpanel@ulinmahoni.com "cd ~/repositories/web-laravel-ulinmahoni && git pull monorepo production"

# 2. If composer.lock changed, reinstall deps (use --ignore-platform-reqs — see PHP-version note)
ssh umadminpanel@ulinmahoni.com "cd ~/repositories/web-laravel-admin-um/Backend && ~/bin/composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs"
ssh umadminpanel@ulinmahoni.com "cd ~/repositories/web-laravel-ulinmahoni/'Frontend API'/web-laravel-ulinmahoni && ~/bin/composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs"

# 3. Clear view + config + route caches
ssh umadminpanel@ulinmahoni.com "find ~/repositories/web-laravel-admin-um/Backend/storage/framework/views/ -name '*.php' -delete"
ssh umadminpanel@ulinmahoni.com "find ~/repositories/web-laravel-ulinmahoni/'Frontend API'/web-laravel-ulinmahoni/storage/framework/views/ -name '*.php' -delete"
ssh umadminpanel@ulinmahoni.com "cd ~/repositories/web-laravel-admin-um/Backend && /usr/local/bin/php artisan config:clear && /usr/local/bin/php artisan route:clear"
ssh umadminpanel@ulinmahoni.com "cd ~/repositories/web-laravel-ulinmahoni/'Frontend API'/web-laravel-ulinmahoni && /usr/local/bin/php artisan config:clear && /usr/local/bin/php artisan route:clear"

# 4. If frontend CSS/JS assets changed, deploy build/ to webroots
#    See "Asset deployment" notes below — manifest + hashed assets must land in each webroot's build/
```

For maintenance windows, use `php artisan down` / `php artisan up` (NOT the `global_title` table — prod's table is a key/value `(idrec, key, mark)` schema and lacks the `maintenance_mode` column from CLAUDE.md's Maintenance Mode section).

### Production webroots

The Laravel app is served via separate webroot directories under `~/public_html/`. The webroot `index.php` files reference the repo paths directly (no symlink); each carries hardcoded paths that must be updated if the repo path layout changes:

| Domain | Webroot | Repo path referenced |
|---|---|---|
| admin.ulinmahoni.com | `~/public_html/admin.ulinmahoni.com/` | `repositories/web-laravel-admin-um/Backend/` |
| ulinmahoni.com (main) | `~/public_html/` | `repositories/web-laravel-ulinmahoni/Frontend API/web-laravel-ulinmahoni/` |
| web.ulinmahoni.com | `~/public_html/web.ulinmahoni.com/` | same as main |
| api.ulinmahoni.com | `~/public_html/api.ulinmahoni.com/` | same as main |

### Asset deployment (Vite `build/`)

`public/build/` is `.gitignored` in both apps, so neither manifest nor hashed assets are pulled by `git pull`. Production resolves this with **symlinks**:
- `~/repositories/web-laravel-admin-um/Backend/public/build` → `~/public_html/admin.ulinmahoni.com/build`
- `~/repositories/web-laravel-ulinmahoni/Frontend API/web-laravel-ulinmahoni/public/build` → `~/public_html/build`

So `npm run build` output that ships to the webroot's `build/` is automatically visible to Laravel via the symlink. When CSS/JS assets change, scp the new `build/` contents into the appropriate webroot:

```bash
# Backend
scp -r Backend/public/build/ umadminpanel@ulinmahoni.com:~/public_html/admin.ulinmahoni.com/build/
# Frontend (main domain)
scp -r 'Frontend API/web-laravel-ulinmahoni/public/build/' umadminpanel@ulinmahoni.com:~/public_html/build/
```

### Known issue: PHP 8.3 prod vs PHP 8.4 composer.lock

Both `composer.lock` files (in `Backend/` and `Frontend API/web-laravel-ulinmahoni/`) were generated on PHP 8.4 — they pin `symfony/clock`, `symfony/string`, etc. at v8.x which require PHP `>=8.4`. Prod runs PHP 8.3.31. The workaround is `composer install --ignore-platform-reqs`, which installs the locked versions anyway; at runtime Laravel 11 still works on 8.3 because no Symfony 8.x package has hit a hard 8.4-only code path yet.

Long-term fix options: (a) upgrade prod to PHP 8.4, (b) regenerate `composer.lock` on PHP 8.3 to pin earlier Symfony 7.x versions. Same gap exists on staging.
