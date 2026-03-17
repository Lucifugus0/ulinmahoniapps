# Multi-Tier Pricing — Progress Tracker

**Last updated**: 2026-03-17 (All phases code complete — verification + UI polish pending)

---

## Phase 1: Database Migrations (Backend)

- [x] **1.1** Create migration: `m_room_pricing_rules` table
  - Columns: `idrec`, `room_id`, `rule_type`, `price`, `date_start`, `date_end`, `label`, `status`, `created_by`, `updated_by`, timestamps
  - Indexes: `(room_id, rule_type)`, `(room_id, date_start, date_end)`
  - File: `Backend/database/migrations/2026_03_17_000001_create_m_room_pricing_rules_table.php`

- [x] **1.2** Create migration: Add multi-pricing columns to `m_rooms`
  - Add: `periode_annual`, `price_original_annual`, `price_discounted_annual`, `price_weekday`, `price_weekend`
  - File: `Backend/database/migrations/2026_03_17_000002_add_multipricing_columns_to_m_rooms.php`

- [x] **1.3** Create migration: Add `price_type` column to `m_room_prices`
  - Add: `price_type VARCHAR(20) NULL DEFAULT 'weekday'`
  - File: `Backend/database/migrations/2026_03_17_000003_add_price_type_to_m_room_prices.php`

- [x] **1.4** Create migration: Add annual + breakdown columns to `t_transactions`
  - Add: `annual_price`, `booking_years`, `price_breakdown` (JSON)
  - File: `Backend/database/migrations/2026_03_17_000004_add_annual_and_breakdown_to_t_transactions.php`

- [x] **1.5** Create migration: Add annual columns to `m_properties`
  - Add: `periode_annual`, `price_original_annual`, `price_discounted_annual`
  - File: `Backend/database/migrations/2026_03_17_000005_add_annual_columns_to_m_properties.php`

- [x] **1.6** Run all migrations on local database
  - Command: `cd Backend && php artisan migrate`

---

## Phase 2: Models & Price Generator Service (Backend)

- [x] **2.1** Create `RoomPricingRule` model
  - Table: `m_room_pricing_rules`
  - Relationships: `belongsTo(Room)`
  - File: `Backend/app/Models/RoomPricingRule.php`

- [x] **2.2** Update `Room` model — add new columns to `$fillable`
  - Add: `periode_annual`, `price_original_annual`, `price_discounted_annual`, `price_weekday`, `price_weekend`
  - Add relationship: `pricingRules()` → hasMany RoomPricingRule
  - File: `Backend/app/Models/Room.php`

- [x] **2.3** Update `RoomPrices` model — add `price_type` to `$fillable`
  - File: `Backend/app/Models/RoomPrices.php`

- [x] **2.4** Update `Transaction` model — add new columns to `$fillable`
  - Add: `annual_price`, `booking_years`, `price_breakdown`
  - Add cast: `price_breakdown` → `array`
  - File: `Backend/app/Models/Transaction.php`

- [x] **2.5** Update `Property` model — add new columns to `$fillable`
  - Add: `periode_annual`, `price_original_annual`, `price_discounted_annual`
  - File: `Backend/app/Models/Property.php`

- [x] **2.6** Create `RoomPriceGeneratorService`
  - Method: `regenerateDailyPrices(int $roomId, ?Carbon $from, ?Carbon $to)`
  - Algorithm: Priority resolution (high_season > low_season > holiday > weekend > weekday)
  - Preserves manual overrides (`price_type = 'manual'`)
  - Upserts into `m_room_prices` with resolved `price` and `price_type`
  - File: `Backend/app/Services/RoomPriceGeneratorService.php`

---

## Phase 3: Data Backfill (Backend)

- [x] **3.1** Create `BackfillPricingRulesSeeder`
  - For each daily room: set `price_weekday = price_weekend = price_original_daily`
  - Create `weekday` and `weekend` pricing rules per room
  - Update existing `m_room_prices` entries with correct `price_type` based on day of week
  - File: `Backend/database/seeders/BackfillPricingRulesSeeder.php`

- [x] **3.2** Run backfill seeder on local database
  - Command: `cd Backend && php artisan db:seed --class=BackfillPricingRulesSeeder`

- [x] **3.3** Verify backfill: spot-check `m_room_pricing_rules` and `m_room_prices.price_type` for existing rooms

---

## Phase 4: Pricing Rules Admin UI (Backend)

### 4a. Controller & Routes

- [x] **4.1** Create `RoomPricingRulesController`
  - `index()` — list all rules for a room
  - `store()` — create new rule (validate rule_type, dates, price)
  - `update()` — update existing rule
  - `destroy()` — delete rule
  - `regenerate()` — trigger `RoomPriceGeneratorService` for room
  - File: `Backend/app/Http/Controllers/Properties/RoomPricingRulesController.php`

- [x] **4.2** Register routes in `Backend/routes/web.php`
  - `GET /rooms/{room}/pricing-rules`
  - `POST /rooms/{room}/pricing-rules`
  - `PUT /rooms/{room}/pricing-rules/{rule}`
  - `DELETE /rooms/{room}/pricing-rules/{rule}`
  - `POST /rooms/{room}/regenerate-prices`

### 4b. Admin Blade UI

- [x] **4.3** Create pricing rules modal Blade component
  - Base Prices section: weekday + weekend inputs
  - Seasons section: table with add/edit/delete for high_season and low_season
  - Holidays section: table with add/edit/delete for holidays
  - Calendar preview with color legend (blue=weekday, purple=weekend, red=high_season, green=low_season, orange=holiday, yellow=manual)
  - Manual date override on click
  - File: `Backend/resources/views/pages/Properties/m-Rooms/components/pricing-rules-modal.blade.php`

- [x] **4.4** Update existing calendar modal to show `price_type` color coding
  - File: `Backend/resources/views/pages/Properties/m-Rooms/components/edit-price-daily-modal.blade.php`

### 4c. Room Create/Edit Form

- [x] **4.5** Update room create modal — add `annual` price_type option
  - When `daily`: show weekday + weekend price fields (required)
  - When `annual`: show annual price field
  - File: `Backend/resources/views/pages/Properties/m-Rooms/index.blade.php`

- [x] **4.6** Update `ManajementRoomsController::store()` — handle annual + weekday/weekend
  - Accept `price_type = 'annual'` → save `price_original_annual`, `periode_annual = 1`
  - When `daily` → save `price_weekday`, `price_weekend`, auto-create pricing rules, call `RoomPriceGeneratorService`
  - File: `Backend/app/Http/Controllers/Properties/ManajementRoomsController.php`

- [x] **4.7** Update `ManajementRoomsController::update()` — same changes as store()
  - File: `Backend/app/Http/Controllers/Properties/ManajementRoomsController.php`

### 4d. Verification

- [ ] **4.8** Test: Create a new daily room with weekday=150k, weekend=200k → verify `m_room_pricing_rules` has 2 rows → verify `m_room_prices` has correct per-date prices for 365 days
- [ ] **4.9** Test: Add a high_season rule (date range + price) → click regenerate → verify affected dates in `m_room_prices` updated
- [ ] **4.10** Test: Add a holiday (single date + price) → verify calendar shows orange for that date
- [ ] **4.11** Test: Create an annual room → verify `periode_annual = 1` and `price_original_annual` saved

---

## Phase 5: Booking Price Calculation (Frontend API — Server Side)

### 5a. Daily Booking: Per-Date Sum

- [x] **5.1** Modify `BookingController::store()` — daily branch
  - Replace `$totalPrice = $price * $bookingDays` with query to `m_room_prices`
  - Sum per-date prices for check_in to check_out-1 range
  - Build `price_breakdown` JSON array
  - Fallback to flat rate if no `m_room_prices` entries (backward compat)
  - File: `Frontend API/app/Http/Controllers/BookingController.php` (lines ~843-862)

- [x] **5.2** Store `price_breakdown` JSON in `t_transactions`
  - File: `Frontend API/app/Http/Controllers/BookingController.php`

### 5b. Annual Booking

- [x] **5.3** Add `rent_type === 'annual'` branch in `BookingController::store()`
  - Calculate: `$totalPrice = $annualPrice * $bookingYears`
  - Set: `$checkOutDate = $checkInDate->addYears($bookingYears)`
  - Store: `annual_price`, `booking_years` in transaction
  - File: `Frontend API/app/Http/Controllers/BookingController.php`

### 5c. Price Preview API

- [x] **5.4** Create price preview endpoint
  - Route: `GET /api/v1/rooms/{roomId}/price-preview?check_in=...&check_out=...`
  - Returns: `{ total_days, total_price, breakdown: [{date, price, type, day_name, label}] }`
  - File: `Frontend API/app/Http/Controllers/Api/RoomController.php` (or new controller)

- [x] **5.5** Register route in `Frontend API/routes/api.php`

### 5d. Room API Enhancement

- [x] **5.6** Add additive fields to room API response
  - Fields: `price_weekday`, `price_weekend`, `price_original_annual`, `periode_annual`, `has_seasonal_pricing`
  - Existing fields unchanged (`price_original_daily` = weekday price for backward compat)
  - File: `Frontend API/app/Http/Controllers/Api/RoomController.php`

### 5e. Frontend API Model Updates

- [x] **5.7** Update Frontend API `Room` model — add new columns to `$fillable`
  - File: `Frontend API/app/Models/Room.php`

- [x] **5.8** Update Frontend API `Transaction` model — add new columns to `$fillable` + casts
  - File: `Frontend API/app/Models/Transaction.php`

### 5f. Verification

- [ ] **5.9** Test: POST booking for daily room spanning weekday+weekend → verify `room_price` = sum of per-date prices (not flat rate)
- [ ] **5.10** Test: POST booking for annual room → verify `annual_price`, `booking_years`, correct `room_price`
- [ ] **5.11** Test: GET price-preview → verify correct breakdown with day names and price types
- [ ] **5.12** Test: Backward compat — book a room without `m_room_prices` entries → verify flat rate fallback works

---

## Phase 6: Frontend Web Changes

- [x] **6.1** Update room detail page — show weekday/weekend prices
  - Add "Prices may vary by date" note when `has_seasonal_pricing`
  - Add `annual` as third rent type option when `periode_annual` enabled
  - File: `Frontend API/resources/views/pages/room/show.blade.php`

- [x] **6.2** Update `updatePriceSummary()` JS function
  - For daily: call `/api/v1/rooms/{roomId}/price-preview` via fetch
  - Display per-date breakdown table (date, day name, type badge, price)
  - Use server total instead of `daily_price × days`
  - For annual: `annualPrice × years`
  - File: `Frontend API/resources/views/pages/room/show.blade.php`

- [x] **6.3** Update booking form hidden inputs for annual bookings
  - Add: `annual_price`, `booking_years` fields
  - File: `Frontend API/resources/views/pages/room/show.blade.php`

- [x] **6.4** Update payment page to show price breakdown when available
  - File: `Frontend API/resources/views/pages/payment/show.blade.php`

### Verification

- [ ] **6.5** Test: Select daily dates on room page → verify price preview API called → breakdown table displayed → correct total
- [ ] **6.6** Test: Select annual booking → verify correct calculation shown
- [ ] **6.7** Test: Complete booking end-to-end → verify transaction has correct `price_breakdown` JSON

---

## Phase 7: Mobile App Changes

- [x] **7.1** Update `RoomModel` — add optional pricing fields
  - Add: `priceWeekday`, `priceWeekend`, `priceOriginalAnnual`, `periodeAnnual`, `hasSeasonalPricing`
  - Parse with null-safety fallback for old API responses
  - File: `Mobile App/lib/features/book/roomdetails/model/rooms_model.dart`

- [x] **7.2** Update `PaymentProvider` — integrate price-preview API for daily bookings
  - Call `/api/v1/rooms/{roomId}/price-preview` instead of client-side flat rate calc
  - Fallback to `originalDailyPrice * duration` if API fails
  - Add annual booking branch
  - File: `Mobile App/lib/features/book/payment/provider/payment_provider.dart`

- [x] **7.3** Update `BookingRequest` model — add annual fields
  - Add: `annualPrice`, `bookingYears` + `toJson()`
  - File: `Mobile App/lib/features/book/payment/model/payment_model.dart`

- [x] **7.4** Add price-preview API endpoint constant
  - File: `Mobile App/lib/core/constants/api_constants.dart`

- [ ] **7.5** Update payment page UI — show per-date breakdown for daily bookings
  - Render: date, day name, price type badge (color-coded), price per line
  - File: `Mobile App/lib/features/book/payment/presentation/pages/paymentpage.dart`

- [ ] **7.6** Update room details page — show weekday/weekend prices + annual option
  - File: `Mobile App/lib/features/book/roomdetails/presentation/pages/roomdetails_page.dart`

### Verification

- [ ] **7.7** Test: Open room detail → select daily dates → verify price breakdown from API displayed
- [ ] **7.8** Test: Complete daily booking → verify correct total
- [ ] **7.9** Test: Book annual room → verify correct `annual_price` and `booking_years` sent
- [ ] **7.10** Test: Old API response (no new fields) → verify app doesn't crash (null-safety fallback)

---

## Phase 8: Scheduled Command (Backend)

- [x] **8.1** Create Artisan command `room-prices:extend`
  - For each daily room: ensure `m_room_prices` entries exist for next 365 days
  - Apply pricing rules to newly generated dates via `RoomPriceGeneratorService`
  - Log which rooms were extended
  - File: `Backend/app/Console/Commands/ExtendRoomPricesCommand.php`

- [x] **8.2** Register command in scheduler (`Backend/app/Console/Kernel.php`)
  - Schedule: daily at midnight

- [ ] **8.3** Test: Run `php artisan room-prices:extend` → verify new date entries created with correct prices

---

## Final Verification Checklist

- [ ] **V1** Backend admin: Create daily room with weekday/weekend pricing → calendar shows correct colors
- [ ] **V2** Backend admin: Add high_season + holiday rules → regenerate → calendar updated
- [ ] **V3** Frontend web: Daily booking across weekday+weekend+holiday → correct itemized total
- [ ] **V4** Frontend web: Annual booking → correct `annual_price × years`
- [ ] **V5** Mobile app: Daily booking with price breakdown display → correct total
- [ ] **V6** Mobile app: Annual booking flow works
- [ ] **V7** Backward compat: Old mobile app books daily room → flat rate fallback succeeds
- [ ] **V8** Edge case: Booking spans season boundary → per-date prices resolve correctly
- [ ] **V9** No existing API signatures changed
- [ ] **V10** No DB columns renamed
- [ ] **V11** All code changes have markdown comments explaining what they do

---

## Summary Stats

| Metric | Count |
|--------|-------|
| New migrations | 5 |
| New files | 5 (model, service, seeder, controller, command) |
| Modified Backend files | 5 (Room model, RoomPrices model, Transaction model, Property model, ManajementRoomsController) |
| New Backend Blade files | 1 (pricing-rules-modal) |
| Modified Backend Blade files | 2 (room index, edit-price-daily-modal) |
| Modified Frontend API files | 4 (BookingController, RoomController, room/show.blade.php, payment/show.blade.php) |
| Modified Mobile App files | 5 (rooms_model, payment_provider, payment_model, api_constants, paymentpage) |
| New API endpoints | 1 (price-preview) |
| Total tasks | 52 |
