# Project Progress — Ulin Mahoni

> Last updated: 2026-03-25


## Branch Status

All branches rebased and synced to `main` at commit `571a7f0`.

| Branch | Owner | Focus Area |
|--------|-------|------------|
| `main` | — | Production-ready, all merges consolidated |
| `farhan-web` | Farhan | Frontend API & web portal |
| `hadrian-admin` | Hadrian | Backend admin dashboard |
| `vincent-mobile-staging` | Vincent | Mobile app (Flutter) |


## Completed Features

### Backend (Admin Dashboard)
- [x] **Maintenance Mode Toggle** — Admin can enable/disable maintenance mode from App Management; updates `global_title` table; Frontend API middleware returns 503 to API/web
- [x] **Sidebar Restructuring** — Flat group hierarchy: 3 standalone items (Dashboard, Room Availability, Chat) + 7 collapsible groups (Bookings, Finance, Promo, Reports, Masters, App Management, Miscellaneous)
- [x] **Access Management Improvements** — Filters to `is_admin=1`, consistent table styling, soft pill badges for roles
- [x] **Daily Room Pricing** — All 5 pricing categories required (weekday, weekend, holiday, high/low season); multi-tier priority algorithm in `RoomPriceGeneratorService`
- [x] **Dark Mode & UI Polish** — Consistent table styling across 9 pages, toggle switch dark mode, sidebar light mode fix, header opacity adjustments
- [x] **Glassmorphism UI** — Apple liquid glass design across Frontend and Backend

### Frontend API (Web Portal & REST API)
- [x] **Firebase Push Notifications** — Full FCM v1 integration with OAuth2 JWT auth, device token management, auto-triggered notifications for bookings/payments/check-ins
- [x] **Device Token API** — `POST/DELETE /api/v1/device-token` for mobile app token registration
- [x] **Push Notification Send API** — `POST /api/v1/push-notification/send` with predefined templates (booking_created, check_in, booking_renewed, payment_received, booking_expired, custom)
- [x] **DOKU Payment Notifications** — VA and QR payment callbacks trigger FCM push to guest + admins
- [x] **Booking Expiry Job** — `ExpireBooking` job sends push notifications on expiry
- [x] **Booking Localization** — Translations for booking pages (ID, EN, ZH)
- [x] **Dark Mode Styling** — Extensive dark mode for booking and payment pages
- [x] **Maintenance Page** — Standalone bilingual (EN + ID) page with Tailwind CDN, animated SVGs
- [x] **Grand Total Price Display** — Shows grand total on booking pages
- [x] **Gender Badge Styling** — Uppercase transaction types, refactored badge styles

### Mobile App (Flutter)
- [x] **Dark Mode** — Full dark mode across 47+ files (login, register, navigation, dialogs, payment, booking, profile); dark mode as default; Material 3 + Apple liquid glass design
- [x] **Chinese Language Support** — Added Simplified Chinese (zh) localization (826 translation keys); app now supports ID, EN, ZH
- [x] **FCM Service** — Token management, foreground/background/terminated message handling, topic subscription, retry logic (3 attempts with exponential backoff)
- [x] **Local Notification Service** — Foreground notification display, chat-specific notifications with conversation ID tracking
- [x] **UI Fixes** — Input field double-layer background fix, button color consistency, language dropdown dark mode, phone number field styling


## Known Issues & TODOs

### Mobile ↔ API Integration Gap (Push Notifications)
- **Endpoint mismatch:** Mobile `FCMRepository` points to `/api/users/fcm-token` but Frontend API expects `/api/v1/device-token`
- **Token sync disabled:** `FCMService` has backend token sync code commented out (TODO markers at lines 70, 78, 104, 106)
- **Action needed:** Update `FCMRepository` endpoints and uncomment token sync in `FCMService`

### iOS Background Tasks
- BGTask processing mode temporarily disabled in `Info.plist` (causing SIGABRT crash due to Bundle ID mismatch)
- See: `feature_guide/BGTASK_TEMPORARY_DISABLE.md`

### iOS Bundle ID Mismatch
- iOS uses `com.ulinmahoni.app` (singular) vs Android `com.ulinmahoni.apps` (plural)

### Backend Firebase
- Backend admin dashboard has no FCM integration — all push notifications go through Frontend API

### Staging Deployment Constraints
- No npm on staging server — must build assets locally and scp
- `php artisan migrate` fails on staging (PHP 8.3 vs 8.4) — run SQL directly
- `php artisan view:clear` may fail — use `find -delete` instead


## Deployment Status

| Environment | Frontend | Backend | Database |
|-------------|----------|---------|----------|
| **Production** | ulinmahoni.com | admin.ulinmahoni.com | umadminpanel_ulinmahoni-db-live |
| **Staging** | staging.ulinmahoni.com | staging-admin.ulinmahoni.com | umadminpanel_ulinmahoni-db-staging |
| **Local** | 127.0.0.1:8001 | 127.0.0.1:8000 | iosadmin_ulinmahoni-demo |
