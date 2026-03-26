# What's New — Ulin Mahoni

## Backend

### New Modules

| Date | Page/Screen | Description |
|------|-------------|-------------|
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
| 2026-03-26 | Chat Dropdown (header widget) | Fixed dark mode transparency — dropdown was see-through due to parent backdrop-filter bleed. Added `isolation: isolate` and solid `background-color` for both chat list and chat window panels. |
| 2026-03-26 | Checked-In Users Modal | Fixed dark mode transparency — increased backdrop opacity (`bg-black/20` → `bg-black/50`, dark: `/70`), added `dark:bg-gray-800` to modal body, dark mode classes to search input, table header/body, and text elements. |
| 2026-03-26 | Add Property (`/properties` modal) | Initial field: increased `maxlength` from 3 to 10 characters (HTML + backend validation). |
| 2026-03-26 | Add Property (`/properties` modal) | Property Type buttons: added solid blue background + white text when selected for clear differentiation in dark mode. |
| 2026-03-26 | Add Property (`/properties` modal) | City dropdown: changed from `<select>` to text input with `<datalist>` — now auto-fills from map pin and accepts new city names. New cities auto-saved to `m_cities` master on property save. |
| 2026-03-26 | Add Property (`/properties` modal) | Facilities: selected items now use solid colored backgrounds (blue/green/purple) with white text instead of subtle tint that was invisible in dark mode. |
| 2026-03-26 | Chat (ChatController, ChatApiController) | Added FCM push notifications when admin sends chat messages to customers. |
| 2026-03-26 | Voucher Management (`/vouchers`) | Fixed pagination showing raw JSON when clicking page 2+. Filter route now redirects non-AJAX requests to index. Added JS handler to intercept pagination clicks and load via AJAX. |

---

## Frontend (Web Portal)

### New Modules

| Date | Page/Screen | Description |
|------|-------------|-------------|
| 2026-03-26 | Tickets Page (`/tickets`) | Full-page customer ticket management — split-pane layout with ticket list (status filter tabs) and chat panel. Supports text/image messages, close/reopen, 10s polling. |
| 2026-03-26 | Floating Ticket Widget | Bottom-right chat bubble on all public pages (authenticated users only). Expands to show ticket list with unread badges, links to `/tickets`. 30s polling. |
| 2026-03-26 | Ticket API (12 endpoints) | `/api/v1/tickets/*` — categories, eligibility, CRUD, messages, close/reopen, mark-as-read. `/api/v1/broadcasts/*` — list, detail. HEIC→JPEG conversion, FCM notifications. |
| 2026-03-26 | Ticket Models (8 models) | Ticket, TicketMessage, TicketAttachment, TicketRead, TicketCategory, TicketSequence, TicketBroadcast, TicketBroadcastRead + Role model. |
| 2026-03-26 | Ticket Service | Business logic for ticket creation, numbering (`{INITIAL}-TKT-{XXXX}`), eligibility checks (paid bookings + 7-day grace), close/reopen lifecycle, staff/HQ CS queries. |

### Bug Fixes

| Date | Page/Screen | Description |
|------|-------------|-------------|
| 2026-03-26 | User Model | Added `role()` relationship (belongsTo Role via `role_id`) for HQ CS user identification. |
| 2026-03-26 | BookingController (renew booking API) | Fixed `Class "App\Http\Controllers\Api\User" not found` error — added missing `use App\Models\User` import. Line 1258 used unqualified `User::find()`. |

---

## Mobile App

### New Modules

| Date | Page/Screen | Description |
|------|-------------|-------------|
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
