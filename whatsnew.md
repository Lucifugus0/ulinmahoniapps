# What's New — Ulin Mahoni

## Backend

### New Modules

| Date | Page/Screen | Description |
|------|-------------|-------------|
| 2026-04-08 | All Pages | Web push notification support via Firebase Cloud Messaging — browser sessions now register FCM tokens and receive push notifications for all events (chat, bookings, etc.). |
| 2026-03-31 | Property & Room Description | Rich text editor (Quill.js) for property and room descriptions — supports bold, italic, text color, bullet/numbered lists, and links. Replaces plain textarea in multi-language description component. |
| 2026-03-31 | Refund Management (`/refund`) | Enhanced refund page — shows Source column (User Request/Admin badge), refund breakdown (room/deposit/parking), bank account details for QRIS/VA user-initiated refunds, admin notes field in confirmation modal. |
| 2026-03-31 | Booking Cancellation (PaymentController) | Admin cancel now uses RefundCalculationService for automatic refund breakdown calculation with tiered percentages. |
| 2026-03-31 | RefundCalculationService | New service for calculating refund amounts — tiered room+parking refund (>10d:75%, 9-7d:50%, 6-3d:25%, <3d:0%), deposit always 100%, service/admin fees not refunded. |
| 2026-03-31 | Database: `t_refund` (migration) | Added columns: `requested_by`, `refund_type`, `refund_bank_name`, `refund_account_no`, `refund_account_holder`, `room_refund`, `deposit_refund`, `other_refund`, `admin_notes`, `processed_by`, `processed_at`. |
| 2026-03-30 | Property & Room CRUD | Multi-language description support (ID/EN/ZH) — tabbed textarea with auto-translate via Google Translate. Descriptions stored as XML-tagged string in existing column. |
| 2026-03-30 | Translation API (`/api/translate`) | Backend endpoint for auto-translating description text between Indonesian, English, and Simplified Chinese using `stichoza/google-translate-php`. |
| 2026-03-27 | Content Management (`/settings/content-management`) | Tagline & hero video management — create/edit/delete taglines (displayed randomly on home pages), upload/activate/delete hero background videos (MP4, max 100MB, 21:9 ratio). Two-tab Alpine.js interface with AJAX CRUD. |
| 2026-03-27 | Content API (`/api/v1/content/*`) | Public endpoints for random tagline and active hero video URL — used by Frontend web and Mobile app. |
| 2026-03-27 | Database: `m_taglines`, `m_hero_videos` | New master tables for dynamic tagline and hero video content management. |
| 2026-03-26 | Ticket Inbox (`/tickets`) | Customer service ticketing system — two-panel layout with ticket list, chat detail, status filters, search. Staff can send text/image messages, close/reopen tickets. Auto-transitions to "in progress" on first staff reply. |
| 2026-03-26 | Broadcast Management (`/tickets/broadcasts`) | One-way announcement system — create, list, and view broadcasts. Supports Front Desk (property-scoped) and HQ audiences. Sends FCM push to all target users. |
| 2026-03-26 | Sidebar: Customer Service | New collapsible sidebar group with Tickets + Broadcasts sub-items, unread badge counter (30s polling). |
| 2026-03-26 | Firebase Notification Service | FCM v1 push notification service for Backend. Sends notifications on chat messages, ticket events, and broadcasts. |
| 2026-03-26 | Ticket Database (8 tables) | `t_tickets`, `t_ticket_messages`, `t_ticket_attachments`, `t_ticket_reads`, `t_ticket_categories`, `t_ticket_sequences`, `t_ticket_broadcasts`, `t_ticket_broadcast_reads`. |
| 2026-03-26 | Ticket Categories Seeder | 12 predefined categories: booking (payment, parking, extend, others), complaint (room, noise, electricity, water, wifi, tv, others), suggestion (feedback). |
| 2026-03-26 | Customer Service Role | New "Customer Service" role seeded in `m_roles` for HQ CS ticket routing. |

### Bug Fixes

| Date | Page/Screen | Description |
|------|-------------|-------------|
| 2026-04-10 | Booking Cancellation Modal (`/payment/pay`) | Fixed text alignment inside Cancel Booking modal — Warning banner heading and message now center-aligned; Booking Details card title and label/value rows now left-aligned; right-column form labels (Cancellation Reason, Refund Calculation, Refund Option, Refund Amount) now left-aligned. |
| 2026-04-09 | Booking Cancellation Modal (`/payment/pay`) | Fixed dark mode header color (was light pink/orange gradient, now solid dark red). Added new cancellation reason "Bukti pembayaran tidak sesuai dan pembayaran tidak ada"; selecting it locks Jumlah Pengembalian Dana to Rp 0 (read-only). |
| 2026-04-08 | Booking Cancellation (PaymentController) | Fixed renewal rollback on admin cancel — when a renewal booking is cancelled, the parent booking's `renewal_status` is now reset to 0 and its `check_out_at` is cleared, matching the Frontend API's existing rollback logic. |
| 2026-03-31 | Payments (`/payments`) | Internationalized all hardcoded strings in payment table — table headers, status badges, action buttons, cancel/reject/edit modals, notes editor, and empty state now use `__('ui.*')` translation keys for ID/EN support. |
| 2026-03-27 | Transaction Report (`/reports/payment`) | Fixed selected/hovered row background color in dark mode — was showing light gray-50 instead of subtle translucent highlight. |
| 2026-03-27 | Property's Rooms (`/m-rooms`) Edit Modal | Fixed modal centering — was trapped inside table by dark mode `backdrop-filter` on tbody. Added `no-backdrop-filter` to tbody. |
| 2026-03-27 | Property's Rooms (`/m-rooms`) Edit Modal | Fixed modal header color in dark mode — added dark blue background to match the blue/indigo gradient theme. |
| 2026-03-27 | Property's Rooms (`/m-rooms`) Edit Modal | Renamed "Price Type" to "Booking Type" in step 2. For daily type, hides price input and shows "Please use daily price management." message. Blocks booking type change if room has active/future bookings. |
| 2026-03-26 | Chat Dropdown (header widget) | Fixed dark mode transparency — dropdown was see-through due to parent backdrop-filter bleed. Added `isolation: isolate` and solid `background-color` for both chat list and chat window panels. |
| 2026-03-26 | Checked-In Users Modal | Fixed dark mode transparency — increased backdrop opacity (`bg-black/20` → `bg-black/50`, dark: `/70`), added `dark:bg-gray-800` to modal body, dark mode classes to search input, table header/body, and text elements. |
| 2026-03-26 | Add Property (`/properties` modal) | Initial field: increased `maxlength` from 3 to 10 characters (HTML + backend validation). |
| 2026-03-26 | Add Property (`/properties` modal) | Property Type buttons: added solid blue background + white text when selected for clear differentiation in dark mode. |
| 2026-03-26 | Add Property (`/properties` modal) | City dropdown: changed from `<select>` to text input with `<datalist>` — now auto-fills from map pin and accepts new city names. New cities auto-saved to `m_cities` master on property save. |
| 2026-03-26 | Add Property (`/properties` modal) | Facilities: selected items now use solid colored backgrounds (blue/green/purple) with white text instead of subtle tint that was invisible in dark mode. |
| 2026-03-26 | Chat (ChatController, ChatApiController) | Added FCM push notifications when admin sends chat messages to customers. |
| 2026-03-26 | Voucher Management (`/vouchers`) | Fixed pagination showing raw JSON when clicking page 2+. Filter route now redirects non-AJAX requests to index. Added JS handler to intercept pagination clicks and load via AJAX. |
| 2026-03-26 | Voucher Management (`/vouchers`) | Removed delete button from action column. Replaced status badge button with toggle switch (checkbox slider) matching Property master page pattern — blue when active, gray when inactive, with text label. |
| 2026-03-26 | Deposit Entry (`/payment/deposit`) | Fixed "Illegal mix of collations" error. `t_deposit_fee_transaction` used `utf8mb4_general_ci` while `t_transactions` used `utf8mb4_unicode_ci`. Added migration to convert table collation to `utf8mb4_unicode_ci`. |
| 2026-03-26 | Parking Entry (`/payment/parking`) | Fixed search/pagination error — filter route only accepted POST, pagination links used GET. Changed to `Route::match(['get','post'])`, added non-AJAX redirect to index, added JS pagination interceptor. |
| 2026-03-26 | Check-in Modal (Confirmed Bookings) | Fixed dark mode header — was nearly transparent (rgba 0.08). Now uses solid dark green background (`rgb(6,78,59)`) with white text for readability. |
| 2026-03-26 | Check-out Modal (Check-out Hari Ini) | Fixed dark mode header — was nearly transparent. Now uses solid dark amber background (`rgb(120,53,15)`) with white text for readability. |
| 2026-03-26 | Check-in Modal body (Confirmed Bookings) | Fixed dark mode body — was `bg-white` without dark class. Added `dark:bg-gray-800` to modal wrapper and `dark:bg-gray-700` to inner panels (guest profile, upload ID). |
| 2026-03-26 | All Modals (global CSS) | Fixed transparent modal bodies caused by global glass CSS rules (`.rounded-lg.border`, `.bg-white.rounded-lg`). Excluded `.shadow-xl` modals from glass rules and added solid white/gray-800 overrides. |
| 2026-03-26 | All Modals inner cards (global CSS) | Fixed inner content cards still too bright in dark mode. Added CSS overrides for `.shadow-xl` child elements: solid `gray-700` bg, `gray-600` borders, no backdrop-filter. |
| 2026-03-26 | Global dark mode (app.css) | Disabled glass CSS rules in dark mode entirely — restricted to `html:not(.dark)`. All `bg-white` elements now solid `gray-800`, `bg-gray-50` now solid `gray-900`. Fixes transparent body/cards on Change Room, modals, and all other pages. |
| 2026-03-26 | Check-in Modal Detail Pemesanan | Removed time from check-in/check-out. Added Total Booking, Deposit, Service Fee. Reorganized into 3-column layout: Booking (Order ID, Guest, Property, Room) · Stay (Check-in, Check-out, Duration) · Payment (Booking, Deposit, Service Fee, Total). |
| 2026-03-26 | Check-out Modal Detail Pemesanan | Added Total Booking, Deposit, Service Fee between Duration and Total Payment. Added `room_price`, `deposit_fee`, `service_fees` to checkout API response. Total Payment now bold green with separator line. |
| 2026-03-26 | Booking Cancellation (`/payment/pay`) | Allow cancellation with Rp 0 refund (no refund). Changed JS validation from `<= 0` to `< 0`. Added missing i18n key `enter_valid_refund_amount` in EN and ID. |
| 2026-03-26 | Daily Pricing Management (`/properties/calendar`) | Added activate/deactivate button to each entry in the "Entri Aktif" list. The `toggleEntryStatus()` JS method existed but had no UI button. Active entries show red "Nonaktifkan", inactive show green "Aktifkan". |
| 2026-03-26 | Room Type Management (`/properties/rooms/room-name-types`) | Fixed "route not found" error on store, update, and toggle-status. Fetch URLs were missing `/properties` prefix (used `/rooms/room-name-types/` instead of `/properties/rooms/room-name-types/`). |
| 2026-03-26 | All Bookings filter (`/bookings/bookings`) | Fixed filter search — `t_booking` used `latin1_swedish_ci` while `t_transactions` used `utf8mb4_general_ci`, causing collation mismatch on LEFT JOIN. Converted to `utf8mb4_general_ci`. Added non-AJAX redirect guard. |
| 2026-03-27 | All Bookings search (`/bookings/bookings`) | Fixed search returning 500 error — `order_id` and `property_id` columns were ambiguous after LEFT JOIN with `t_transactions`. Prefixed with `t_booking.` table name in both `index()` and `filter()` methods. |
| 2026-03-27 | Today's Check-Out search (`/bookings/checkout`) | Same ambiguous column fix — prefixed `order_id` and `property_id` with `t_booking.` in both `index()` and `filter()` methods. |
| 2026-03-27 | Deposit Entry (`/payment/deposit`) | Fixed "Illegal mix of collations" error — 24 tables used `utf8mb4_general_ci` while others used `utf8mb4_unicode_ci`, causing JOIN errors. Converted all tables to `utf8mb4_unicode_ci`. |
| 2026-03-27 | Language Switcher (global) | Fixed locale not persisting — `SetLocale` middleware now syncs DB locale to session as backup, and `updateLocale` also writes to session. Prevents revert to default `id` on transient auth failures. |
| 2026-03-27 | Deposit Entry pagination (`/payment/deposit`) | Fixed 405 "Method Not Allowed" on pagination — filter route only accepted POST but pagination links use GET. Changed to `Route::match(['get','post'])` and added non-AJAX redirect guard. |
| 2026-03-27 | Header (global) | Changed header to glassmorphism style — 35% opacity with 48px backdrop blur for both light and dark modes. Blur styles in inline `<style>` block because Vite/Lightning CSS strips unprefixed `backdrop-filter` needed by Firefox. |
| 2026-03-26 | Today's Check-Out filter (`/bookings/checkout`) | Same collation fix. Added non-AJAX redirect guard to filter method. |

---

## Frontend (Web Portal)

### New Modules

| Date | Page/Screen | Description |
|------|-------------|-------------|
| 2026-04-08 | All Pages | Web push notification support via Firebase Cloud Messaging — authenticated browser sessions now register FCM tokens and receive push notifications for all events (bookings, payments, etc.). |
| 2026-04-07 | Room Availability (API + Views) | Changed availability logic: daily rooms (`periode_daily=1`) are always shown as "Tersedia" (available). Monthly-only rooms compute availability dynamically by checking `t_booking` + `t_transactions` for active bookings instead of the static `rental_status` flag. New `is_available` field added to API responses alongside existing `rental_status` for backward compatibility. Affected: SearchController, BookingController, PropertyController, HomeController, HouseController, ApartController, RoomController, and all property/room blade templates. |
| 2026-04-07 | Property Detail (`/houses/{id}`) | Room category accordions now default to showing only available rooms. Added "Show all rooms" checkbox inside each category to reveal unavailable rooms. Uses Alpine.js `x-show` filtering. Translated in ID/EN/ZH. |

| Date | Page/Screen | Description |
|------|-------------|-------------|
| 2026-03-31 | My Bookings (`/bookings`) | User-initiated booking cancellation — "Batalkan" button on Upcoming tab (pending/waiting) and Completed tab (paid, not checked-in). SweetAlert2 modal shows refund breakdown preview, bank account form for QRIS/VA, and confirmation. |
| 2026-03-31 | Cancel API (`/api/v1/booking/{order_id}/cancel`) | New POST endpoint for cancelling bookings. Creates refund record, releases room and parking, sends FCM notifications. |
| 2026-03-31 | Cancel Preview API (`/api/v1/booking/{order_id}/cancel-preview`) | New GET endpoint returning refund breakdown calculation without cancelling. |
| 2026-03-31 | RefundCalculationService | Shared refund calculation service — tiered percentages for room+parking, 100% deposit, detects QRIS/VA for bank account requirement. |
| 2026-03-31 | Refund Model | New Eloquent model for `t_refund` table with user-initiated refund fields and relationships. |
| 2026-03-31 | FCM: `booking_cancelled` | New notification type added to push notification templates. |
| 2026-03-31 | Property & Room Description | Rich text HTML rendering for descriptions — `DescriptionHelper::getHtml()` sanitizes HTML (allows bold, italic, color, lists, links) while stripping dangerous tags. Backward compatible with plain text. Updated all 14 blade templates (property, room, house, villa, apartment, hotel). |
| 2026-03-30 | Property & Room Detail | Multi-language description display — shows description based on user's language selector (ID/EN/ZH). Fallback chain: selected locale -> EN -> ID. Added line break support via `nl2br`. |
| 2026-03-30 | Property & Room API | Added `description_parsed` / `descriptions_parsed` fields to API responses with per-language breakdown for mobile app. |
| 2026-03-27 | Home Page | Dynamic tagline loaded from database (random active tagline). Removed duplicate tagline text below hero heading. |
| 2026-03-27 | Home Page | Dynamic hero video — loads active video from Backend storage, falls back to bundled default video. |
| 2026-03-26 | Tickets Page (`/tickets`) | Full-page customer ticket management — split-pane layout with ticket list (status filter tabs) and chat panel. Supports text/image messages, close/reopen, 10s polling. |
| 2026-03-26 | Floating Ticket Widget | Bottom-right chat bubble on all public pages (authenticated users only). Expands to show ticket list with unread badges, links to `/tickets`. 30s polling. |
| 2026-03-26 | Ticket API (12 endpoints) | `/api/v1/tickets/*` — categories, eligibility, CRUD, messages, close/reopen, mark-as-read. `/api/v1/broadcasts/*` — list, detail. HEIC→JPEG conversion, FCM notifications. |
| 2026-03-26 | Ticket Models (8 models) | Ticket, TicketMessage, TicketAttachment, TicketRead, TicketCategory, TicketSequence, TicketBroadcast, TicketBroadcastRead + Role model. |
| 2026-03-26 | Ticket Service | Business logic for ticket creation, numbering (`{INITIAL}-TKT-{XXXX}`), eligibility checks (paid bookings + 7-day grace), close/reopen lifecycle, staff/HQ CS queries. |

### Bug Fixes

| Date | Page/Screen | Description |
|------|-------------|-------------|
| 2026-04-13 | Transaction Model (`app/Models/Transaction.php`) | Fixed monthly renewal end-of-month drift on production — `original_checkin_day` was missing from `$fillable`, so mass assignment silently dropped it on every `Transaction::create()`. Every renewal since 2026-04-01 was storing NULL, causing the mobile app to fall back to current check-in day and drift the billing day across chains (e.g. Jan 31 → Feb 28 → Mar 28 instead of Mar 31). Added `'original_checkin_day'` to `$fillable` and backfilled the 70 affected production rows using chain-ancestor lookup (same user+room, walks back to last known OCD, fallback to earliest chain check-in day). |
| 2026-04-10 | ExpireBooking Job | Fixed renewal rollback on booking expiry — when a renewal transaction expires due to unpaid timeout, the parent booking's `renewal_status` is now reset to 0 and `check_out_at` is cleared (and `m_rooms.rental_status` restored for monthly rooms), so the user can renew the parent booking again. Mirrors the existing cancel-flow rollback added in c309eba. |
| 2026-03-26 | User Model | Added `role()` relationship (belongsTo Role via `role_id`) for HQ CS user identification. |
| 2026-03-26 | BookingController (renew booking API) | Fixed `Class "App\Http\Controllers\Api\User" not found` error — added missing `use App\Models\User` import. Line 1258 used unqualified `User::find()`. |
| 2026-03-26 | Payment Page (`/payment/show`) | Fixed dark mode not applying — body had no background/text classes. Added `dark:bg-gray-900 dark:text-gray-100` to body, section, and headings. Removed empty `class=""` from `<html>` tag. |
| 2026-03-26 | Property Listing (gender badge) | Fixed gender badge (♂♀ label) hard to see in dark mode. Added semi-transparent white background, brighter text, and visible border. |
| 2026-03-27 | Header (homepage) | Changed `--glass-bg` from 18% to 35% opacity for light mode header, matching backend. |
| 2026-03-27 | Room Detail (daily booking) | Fixed check-out date picker becoming unresponsive after changing check-in date. Replaced `setOptions()` with destroy/recreate pattern for vanillajs-datepicker. Applied to both ID and EN versions. |

---

## Mobile App

### New Modules

| Date | Page/Screen | Description |
|------|-------------|-------------|
| 2026-03-31 | Booking Detail Page | User-initiated booking cancellation — "Batalkan Booking" red button on detail page for pending/waiting/paid (not checked-in) bookings. Opens cancel dialog with refund breakdown preview, bank account form for QRIS/VA, and confirmation. |
| 2026-03-31 | Cancel Booking Dialog | New dialog widget showing tiered refund calculation (room, deposit, parking), QRIS/VA bank account inputs, cancel confirmation. Refreshes booking list on success. |
| 2026-03-31 | Cancel Booking Data Layer | Repository (`cancel_booking_repository.dart`), model (`cancel_refund_model.dart`), provider (`cancel_booking_provider.dart`) — Riverpod 3.x Notifier pattern. |
| 2026-03-31 | Property & Room Description | Rich text HTML rendering for descriptions using `flutter_widget_from_html_core`. New `HtmlDescription` widget auto-detects HTML vs plain text content. Updated property detail and room detail pages. |
| 2026-03-30 | Room & Property Detail | Multi-language description support — displays description based on app locale (ID/EN/ZH) using `descriptions_parsed`/`description_parsed` from API. Fallback: current locale -> EN -> ID -> raw. |
| 2026-03-27 | Home Page | Dynamic tagline loaded from API (random active tagline), falls back to localized 3-part text. |
| 2026-03-27 | Home Page | Hero video caching — checks API for active video, downloads in background for next launch. Uses bundled asset as default fallback. |
| 2026-03-27 | Bottom Navigation Bar | Moved menu bar lower by reducing bottom margin from 12px to 0px. |
| 2026-03-26 | Ticket List Page (`/cs`) | Main customer service page with "My Tickets" and "Broadcasts" tabs. Shows ticket cards with status badges, unread counts, category labels. FAB for new ticket creation. |
| 2026-03-26 | Create Ticket Page (`/cs/create`) | Multi-step wizard: select type → select booking → select category → enter subject + message. Supports Suggestion Box (no booking required). |
| 2026-03-26 | Ticket Chat Page (`/cs/ticket/:id`) | Chat room for tickets — message bubbles, image send (camera/gallery), close/reopen actions, 5s polling. System messages for status changes. |
| 2026-03-26 | Broadcast Detail Page (`/cs/broadcast/:id`) | Read-only announcement view with title, message, sender info, metadata. Marks as read on open. |
| 2026-03-26 | Ticket Data Layer | 5 models (ticket, message, category, broadcast, eligible booking), repository (13 API methods), controller (StateNotifier), 6 Riverpod providers. |
| 2026-03-26 | API Constants | 12 new ticket/broadcast endpoint constants added. |

### Bug Fixes

| Date | Page/Screen | Description |
|------|-------------|-------------|
| 2026-03-26 | FCM Token Sync | Fixed endpoint mismatch: mobile was using `/api/users/fcm-token` but server expects `/api/v1/device-token`. Rewrote `FCMRepository` to use correct payload (`token`, `device_type`, `device_name`) with `DioClient` (auto-auth). |
| 2026-03-26 | FCM Service | Enabled token sync code (was commented out). Token now registers on login via `syncTokenToBackend()` and deletes on logout. |
| 2026-03-26 | Auth Provider | Added FCM token sync after successful login, FCM token cleanup on logout (was commented out). |
| 2026-03-27 | Ticket List Page (`/cs`) | Fixed "New Ticket" FAB hidden behind bottom nav bar. Added bottom margin (100px) to account for ShellRoute's BottomNavBar with `extendBody:true`. |
| 2026-03-27 | Register Page (signup) | Phone number input now follows frontend signup rules: strips leading zeros (matching frontend `replace(/^0+/, '')`), removed artificial "/15" max length counter, updated placeholder to "8123456789". |
| 2026-04-06 | Home Page | Fixed glass navbar transparency — disabled top SafeArea so content scrolls behind the navbar, enabling BackdropFilter blur effect to work. Added top padding in VideoSearchBanner so greeting text appears below navbar. |
| 2026-04-06 | Room Detail Page | Moved "Fasilitas Ruangan" section above "Biaya Tambahan" for better layout flow. |
| 2026-04-06 | Room Detail Page | Fixed daily pricing: subtotal now uses `multiTierTotalPrice` from price-preview API (per-date weekday/weekend/holiday rates) instead of flat rate × duration. |
| 2026-04-06 | Room Detail Page | Added daily pricing breakdown section under "Informasi Pemesanan" showing per-date prices with day type badges (weekday/weekend/holiday/seasonal). |
| 2026-04-06 | Payment Page | Replaced "Harga Harian" single line with per-day pricing breakdown table for daily bookings. Monthly bookings retain the original layout. |
| 2026-04-07 | Payment Page | Fixed DOKU gateway (VA/QRIS/CC) receiving flat rate × duration instead of multi-tier total. All payment method calls now use `_confirmedTotal` (set from `displayedTotal` at Bayar tap). |
| 2026-04-07 | Payment Page (renewal) | Fixed renewal DOKU amount — was using backend's flat `grandTotal`. Now uses `_renewalMultiTierTotalPrice` from price-preview API in `displayedTotal`. |
| 2026-04-07 | Renew Booking Dialog | Added per-day price breakdown (DailyPriceBreakdown widget) and subtotal for daily bookings. Breakdown is fetched from price-preview API when dates change. |
| 2026-04-07 | Cancel Booking Dialog | Fixed "gagal membatalkan booking" false error after successful cancel — autoDispose provider was disposed during async API call (no listener). Fixed by watching provider in build(). Added AppLogger tracing throughout cancel flow. |
| 2026-03-29 | Dependencies | Ran `flutter pub upgrade` — updated 50 packages (minor/patch). Key updates: dio 5.9.2, camera 0.11.4, shared_preferences 2.5.5, video_player 2.11.1, flutter_svg 2.2.4, google_sign_in_ios 6.3.0, logger 2.7.0. |
| 2026-03-29 | Payment Providers | Migrated `DokuCCNotifier`, `DokuQRISNotifier`, `DokuVANotifier`, `PaymentNotifier`, `VoucherNotifier` from `StateNotifier`/`StateNotifierProvider` to Riverpod 3.x `Notifier`/`NotifierProvider`. |
| 2026-03-29 | Room Details Providers | Migrated `RoomDetailsNotifier`, `AvailabilityCheckNotifier` from `StateNotifier`/`StateNotifierProvider` to Riverpod 3.x `Notifier`/`NotifierProvider`. |
| 2026-03-29 | Room Filter (Detail Property) | Migrated `selectedRoomFilterProvider` and `selectedRoomStatusFilterProvider` from `StateProvider.family` to Riverpod 3.x family `Notifier`/`NotifierProvider.family`. |
| 2026-03-29 | Room Details Page, Video Banner | Replaced deprecated `.valueOrNull` with `.value` (nullable by default in Riverpod 3.x). |
| 2026-03-29 | Room Detail, Property Detail | Multi-language descriptions — room and property models now parse `descriptions_parsed` / `description_parsed` from API. Detail pages resolve description by app locale (`id`, `en`, `zh`) with fallback chain. |
