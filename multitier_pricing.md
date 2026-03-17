# Multi-Tier Pricing System — Implementation Plan

## Context

The app currently supports only **daily** (flat rate × days) and **monthly** (flat rate × months) pricing. The goal is to extend this to:

1. **Annual** pricing (single price × years)
2. **Monthly** pricing (unchanged — single price × months)
3. **Daily** multi-tier pricing with priority resolution:
   - High Season (overrides everything for a date range)
   - Low Season (overrides everything for a date range)
   - Public Holiday (specific dates, if not in a season)
   - Weekend (Sat/Sun, if not in season or holiday)
   - Weekday (Mon-Fri, default fallback)

Admin enters dates/ranges for holidays and seasons. The system resolves a final price per date and stores it in `m_room_prices`. Booking price = sum of per-date prices.

**Constraints**: No existing API changes, no DB column renames, all changes additive.

---

## Phase 1: Database Migration (Backend)

### 1a. New table: `m_room_pricing_rules`

Stores admin-configured pricing rules per room.

```
idrec           INT UNSIGNED AUTO_INCREMENT PK
room_id         INT NOT NULL                -- FK m_rooms.idrec
rule_type       VARCHAR(20) NOT NULL        -- 'weekday','weekend','holiday','high_season','low_season'
price           DECIMAL(18,4) NOT NULL
date_start      DATE NULL                   -- NULL for weekday/weekend (always apply)
date_end        DATE NULL                   -- NULL for weekday/weekend; = date_start for single-day holiday
label           VARCHAR(100) NULL           -- e.g. "Eid al-Fitr", "Christmas Peak"
status          TINYINT DEFAULT 1
created_by      INT NULL
updated_by      INT NULL
created_at      TIMESTAMP NULL
updated_at      TIMESTAMP NULL

INDEX (room_id, rule_type)
INDEX (room_id, date_start, date_end)
```

**File**: `Backend/database/migrations/YYYY_MM_DD_create_m_room_pricing_rules_table.php`

### 1b. New columns on `m_rooms` (additive)

```sql
ADD COLUMN periode_annual       TINYINT NULL DEFAULT 0
ADD COLUMN price_original_annual DECIMAL(18,4) NULL
ADD COLUMN price_discounted_annual DECIMAL(18,4) NULL
ADD COLUMN price_weekday        DECIMAL(18,4) NULL
ADD COLUMN price_weekend        DECIMAL(18,4) NULL
```

**File**: `Backend/database/migrations/YYYY_MM_DD_add_multipricing_columns_to_m_rooms.php`

### 1c. New column on `m_room_prices` (additive)

```sql
ADD COLUMN price_type VARCHAR(20) NULL DEFAULT 'weekday'
-- Values: weekday, weekend, holiday, high_season, low_season, manual
```

**File**: `Backend/database/migrations/YYYY_MM_DD_add_price_type_to_m_room_prices.php`

### 1d. New columns on `t_transactions` (additive)

```sql
ADD COLUMN annual_price     DECIMAL(18,4) NULL
ADD COLUMN booking_years    INT NULL
ADD COLUMN price_breakdown  JSON NULL   -- [{date, price, type}, ...]
```

**File**: `Backend/database/migrations/YYYY_MM_DD_add_annual_and_breakdown_to_t_transactions.php`

### 1e. New columns on `m_properties` (additive)

```sql
ADD COLUMN periode_annual           TINYINT NULL DEFAULT 0
ADD COLUMN price_original_annual    DECIMAL(18,4) NULL
ADD COLUMN price_discounted_annual  DECIMAL(18,4) NULL
```

---

## Phase 2: Price Generator Service (Backend)

**New file**: `Backend/app/Services/RoomPriceGeneratorService.php`

Core method: `regenerateDailyPrices(int $roomId, ?Carbon $from, ?Carbon $to)`

**Algorithm** for each date in range (default: today → today+365):
1. If date has a `manual` override in `m_room_prices` → keep it
2. Else check `m_room_pricing_rules` in priority order:
   - `high_season` range containing this date → use that price
   - `low_season` range containing this date → use that price
   - `holiday` range containing this date → use that price
   - Date is Sat/Sun → use `weekend` rule price
   - Otherwise → use `weekday` rule price
3. Upsert into `m_room_prices` with resolved `price` and `price_type`

**Called by**: Room create/update, pricing rule CRUD, daily cron job.

---

## Phase 3: Data Backfill (Backend)

**New file**: `Backend/database/seeders/BackfillPricingRulesSeeder.php`

For each existing daily room:
- Set `price_weekday = price_original_daily`, `price_weekend = price_original_daily`
- Create 2 `m_room_pricing_rules` rows: `weekday` and `weekend` with same price
- Update existing `m_room_prices` entries to set `price_type = 'weekday'` (or `'weekend'` for Sat/Sun dates)

Existing `m_room_prices` prices remain untouched — backfill is metadata only.

---

## Phase 4: Pricing Rules Admin UI (Backend)

### 4a. New controller

**New file**: `Backend/app/Http/Controllers/Properties/RoomPricingRulesController.php`

Routes (under authenticated admin middleware):
- `GET  /rooms/{room}/pricing-rules` → index (list rules)
- `POST /rooms/{room}/pricing-rules` → store (create rule)
- `PUT  /rooms/{room}/pricing-rules/{rule}` → update
- `DELETE /rooms/{room}/pricing-rules/{rule}` → destroy
- `POST /rooms/{room}/regenerate-prices` → trigger regeneration

### 4b. New Blade component

**New file**: `Backend/resources/views/pages/Properties/m-Rooms/components/pricing-rules-modal.blade.php`

UI sections:
- **Base Prices**: Weekday + Weekend price inputs
- **Seasons**: Table of high/low seasons with label, date range picker, price. Add/edit/delete.
- **Holidays**: Table of holidays with label, date (or range), price. Add/edit/delete.
- **Calendar Preview**: Extends existing `edit-price-daily-modal.blade.php` calendar with color legend (blue=weekday, purple=weekend, red=high season, green=low season, orange=holiday, yellow=manual)
- **Manual Override**: Click any date in calendar to set a custom price

### 4c. Room form update

**Modify**: `Backend/resources/views/pages/Properties/m-Rooms/index.blade.php`
- Add `annual` as third `price_type` option
- When `daily` selected: show weekday + weekend price fields (required)
- When `annual` selected: show annual price field

**Modify**: `Backend/app/Http/Controllers/Properties/ManajementRoomsController.php`
- `store()`: Handle `price_type = 'annual'`, save `price_original_annual`, `periode_annual`
- `store()`: When daily, also save `price_weekday`, `price_weekend`, create pricing rules, call `RoomPriceGeneratorService`
- `update()`: Same changes

### 4d. Model updates

**Modify**: `Backend/app/Models/Room.php` — add new columns to `$fillable`
**New file**: `Backend/app/Models/RoomPricingRule.php` — model for `m_room_pricing_rules`

---

## Phase 5: Booking Price Calculation (Frontend API — Server Side)

### 5a. Daily booking: sum per-date prices

**Modify**: `Frontend API/app/Http/Controllers/BookingController.php` (lines ~843-862)

Replace:
```php
$totalPrice = $price * $bookingDays;
```

With:
```php
// Query m_room_prices for each date in booking range
$datePrices = DB::table('m_room_prices')
    ->where('room_id', $roomId)
    ->whereBetween('date', [$checkIn, $checkOut->subDay()])
    ->where('status', 1)
    ->get(['date', 'price', 'price_type']);

$totalPrice = $datePrices->sum('price');
$priceBreakdown = $datePrices->map(fn($p) => [...]);

// Fallback: if no m_room_prices entries, use flat rate (backward compat)
if ($datePrices->isEmpty()) {
    $totalPrice = $price * $bookingDays;
}
```

Store `price_breakdown` JSON in `t_transactions`.

### 5b. Annual booking: new branch

Add `rent_type === 'annual'` branch alongside daily/monthly:
```php
$bookingYears = (int) $request->years;
$checkOutDate = $checkInDate->copy()->addYears($bookingYears);
$totalPrice = $annualPrice * $bookingYears;
```

### 5c. New API endpoint: Price Preview

**New route**: `GET /api/v1/rooms/{roomId}/price-preview?check_in=...&check_out=...`

Returns per-date breakdown with total. Used by frontend web and mobile for live price display before booking.

**Response format**:
```json
{
    "status": "success",
    "data": {
        "room_id": 42,
        "check_in": "2026-04-01",
        "check_out": "2026-04-05",
        "total_days": 4,
        "total_price": 650000,
        "breakdown": [
            {"date": "2026-04-01", "price": 150000, "type": "weekday", "day_name": "Wednesday"},
            {"date": "2026-04-02", "price": 150000, "type": "weekday", "day_name": "Thursday"},
            {"date": "2026-04-03", "price": 200000, "type": "holiday", "day_name": "Friday", "label": "Good Friday"},
            {"date": "2026-04-04", "price": 150000, "type": "weekend", "day_name": "Saturday"}
        ]
    }
}
```

### 5d. Room API response enhancement

**Modify**: `Frontend API/app/Http/Controllers/Api/RoomController.php`

Add additive fields to room response:
```json
"price_weekday": 150000,
"price_weekend": 180000,
"price_original_annual": 15000000,
"periode_annual": 1,
"has_seasonal_pricing": true
```

Existing fields (`price_original_daily`, `price_original_monthly`) remain unchanged. `price_original_daily` = weekday price for backward compat.

---

## Phase 6: Frontend Web Changes

### 6a. Room detail page

**Modify**: `Frontend API/resources/views/pages/room/show.blade.php`

- Show weekday/weekend prices when available
- Add "Prices may vary by date" note for rooms with seasonal pricing
- Add `annual` as third rent type option when `periode_annual` is enabled

### 6b. Booking price calculation (client-side JS)

**Modify**: `Frontend API/resources/views/pages/room/show.blade.php` (JS function `updatePriceSummary()`)

For daily bookings: call `/api/v1/rooms/{roomId}/price-preview` via fetch, display per-date breakdown, use server total.

For annual: `annualPrice × years`.

---

## Phase 7: Mobile App Changes

### 7a. Model updates

**Modify**: `Mobile App/lib/features/book/roomdetails/model/rooms_model.dart`

Add optional fields: `priceWeekday`, `priceWeekend`, `priceOriginalAnnual`, `periodeAnnual`, `hasSeasonalPricing`. Parse with null-safety fallback.

### 7b. Payment provider

**Modify**: `Mobile App/lib/features/book/payment/provider/payment_provider.dart`

For daily bookings: call price-preview API, use server-returned total. Fallback to flat rate if API fails.

Add annual booking branch.

### 7c. BookingRequest model

**Modify**: `Mobile App/lib/features/book/payment/model/payment_model.dart`

Add: `annualPrice`, `bookingYears` fields + `toJson()`.

### 7d. UI — price breakdown display

**Modify**: `Mobile App/lib/features/book/payment/presentation/pages/paymentpage.dart`

When daily breakdown available: render itemized list (date, day name, price type badge, price).

---

## Phase 8: Scheduled Command

**New file**: `Backend/app/Console/Commands/ExtendRoomPricesCommand.php`

Runs daily via cron. For each daily room, extends `m_room_prices` entries to cover next 365 days using `RoomPriceGeneratorService`.

---

## Implementation Order

| Step | Scope | Description |
|------|-------|-------------|
| 1 | Backend | Database migrations (new table + additive columns) |
| 2 | Backend | `RoomPricingRule` model + `RoomPriceGeneratorService` |
| 3 | Backend | Backfill seeder for existing rooms |
| 4 | Backend | Pricing rules controller + routes |
| 5 | Backend | Admin Blade UI (pricing rules modal, room form updates) |
| 6 | Backend | Scheduled command for price extension |
| 7 | Frontend API | Price-preview API endpoint |
| 8 | Frontend API | `BookingController::store()` — per-date sum for daily |
| 9 | Frontend API | Room API response enhancement (additive fields) |
| 10 | Frontend API | Room detail page + booking JS updates |
| 11 | Mobile App | Model + provider + UI updates |

---

## Price Resolution Priority (for a given date)

```
1. High Season range → high_season_price
2. Low Season range  → low_season_price
3. Public Holiday    → holiday_price
4. Weekend (Sat/Sun) → weekend_price
5. Weekday (Mon-Fri) → weekday_price
```

High/Low season **invalidates** weekday/weekend prices — the season price applies to ALL days in that range regardless of day of week.

---

## Backward Compatibility Strategy

- **Existing API responses**: All new fields are additive. `price_original_daily` continues to return weekday price.
- **Old mobile app**: Still sends `daily_price × days`. Server accepts this as fallback when `m_room_prices` has no entries.
- **Existing rooms**: Backfill seeder sets `price_weekday = price_weekend = price_original_daily`, so resolved per-date prices match the old flat rate.
- **Monthly/Annual**: Flat rate calculation unchanged — no per-date resolution needed.

---

## Verification

1. **Backend admin**: Create a room with daily pricing → set weekday/weekend/holiday/season prices → verify calendar shows correct color-coded prices → verify `m_room_prices` has correct resolved prices
2. **Frontend web**: View room detail → select daily dates spanning weekday+weekend+holiday → verify price preview shows itemized breakdown → complete booking → verify `t_transactions.price_breakdown` JSON is correct
3. **Mobile app**: Same flow as web — verify price breakdown display and correct total
4. **Backward compat**: Old mobile app (without update) books a daily room → API falls back to flat `daily_price × days` → booking succeeds
5. **Annual booking**: Create annual room → book for 1 year → verify `booking_years`, `annual_price`, `room_price` in transaction
6. **Edge cases**: Book across season boundary (3 days weekday + 2 days high season) → verify correct per-date prices applied

---

## Key Files Reference

### Backend
- `Backend/app/Models/Room.php` — Room model, add new columns to $fillable
- `Backend/app/Models/RoomPrices.php` — Existing per-date price model
- `Backend/app/Http/Controllers/Properties/ManajementRoomsController.php` — Room CRUD + price management
- `Backend/resources/views/pages/Properties/m-Rooms/index.blade.php` — Room create/edit form
- `Backend/resources/views/pages/Properties/m-Rooms/components/edit-price-daily-modal.blade.php` — Price calendar UI
- `Backend/database/migrations/2025_06_25_111058_create_m_room_prices_table.php` — Existing m_room_prices migration

### Frontend API
- `Frontend API/app/Http/Controllers/BookingController.php` — Booking creation + price calc (lines 843-862)
- `Frontend API/app/Http/Controllers/Api/BookingController.php` — API booking controller
- `Frontend API/resources/views/pages/room/show.blade.php` — Room detail + booking form + JS price calc
- `Frontend API/app/Models/Room.php` — Frontend Room model
- `Frontend API/app/Models/Transaction.php` — Transaction model

### Mobile App
- `Mobile App/lib/features/book/roomdetails/model/rooms_model.dart` — Room data model
- `Mobile App/lib/features/book/payment/provider/payment_provider.dart` — Price calculation logic
- `Mobile App/lib/features/book/payment/model/payment_model.dart` — BookingRequest model
- `Mobile App/lib/features/book/payment/presentation/pages/paymentpage.dart` — Payment UI
- `Mobile App/lib/core/constants/api_constants.dart` — API endpoint config
