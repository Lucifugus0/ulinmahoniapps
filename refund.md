# Cancellation & Refund Feature — Implementation Plan

## Context

Users currently cannot cancel bookings themselves — cancellation is admin-only via the Backend dashboard. This feature adds user-initiated cancellation with automatic refund calculation across all three apps (Backend, Frontend API, Mobile App). The refund amount is calculated based on how many days remain before check-in, with different rules for room charges, deposit, and other fees.

## Refund Calculation Rules

| Component | Rule |
|-----------|------|
| **Room price** | >10 days before check-in: 75%, 9–7 days: 50%, 6–3 days: 25%, <3 days: 0% |
| **Deposit** | Always 100% refund |
| **Parking/others** | Same as room price: >10 days: 75%, 9–7 days: 50%, 6–3 days: 25%, <3 days: 0% |
| **Service fees, admin fees** | NOT refunded |

**Refund method:** Returned to original payment method. For QRIS and VA, user must provide bank account (bank name, account number, holder name) — bank admin fee applies.

**Cancellable statuses:**
- `paid` (not yet checked-in) → cancel with refund calculation
- `pending` / `waiting` → cancel immediately, no refund needed, release room

---

## Phase 1: Database Migration

### 1.1 Add columns to `t_refund` table

**File:** New migration in `Backend/database/migrations/`

Add columns for user-initiated refunds and bank account details:
```
- requested_by       INT NULLABLE          -- user_id who requested (NULL = admin-initiated)
- refund_type        ENUM('admin','user')   DEFAULT 'admin'
- refund_bank_name   VARCHAR(100) NULLABLE  -- for QRIS/VA refunds
- refund_account_no  VARCHAR(50)  NULLABLE
- refund_account_holder VARCHAR(100) NULLABLE
- room_refund        DECIMAL(18,4) NULLABLE -- room price refund amount
- deposit_refund     DECIMAL(18,4) NULLABLE -- deposit refund amount
- other_refund       DECIMAL(18,4) NULLABLE -- parking/other refund amount
- admin_notes        TEXT NULLABLE          -- admin notes when processing
- processed_by       INT NULLABLE           -- admin user_id who processed
- processed_at       DATETIME NULLABLE
```

### 1.2 Update Refund model

**File:** `Backend/app/Models/Refund.php`

Add new columns to `$fillable`. Add `requestedBy()` and `processedBy()` relationships.

---

## Phase 2: Backend (Admin Dashboard)

### 2.1 Refund calculation service

**File:** New `Backend/app/Services/RefundCalculationService.php`

Reusable service with a `calculate(Transaction $transaction): array` method that returns:
```php
[
    'room_refund' => ...,
    'deposit_refund' => ...,
    'other_refund' => ...,  // parking etc.
    'total_refund' => ...,
    'days_before_checkin' => ...,
    'refund_percentage' => ...,  // for room price
    'other_refund_percentage' => ...,  // for parking
]
```

Logic:
- Calculate days between now and `check_in` date
- Apply tiered percentage to `room_price` (>10d: 75%, 9–7d: 50%, 6–3d: 25%, <3d: 0%)
- Apply same tiered percentage to `parking_fee` and other fees
- Deposit (`deposit_fee`) always 100%
- Sum all components for `total_refund`

### 2.2 Update Backend refund index page

**File:** `Backend/resources/views/pages/payment/refund/index.blade.php`

- Add columns: "Requested By" (user/admin), "Refund Breakdown" (room/deposit/other amounts)
- For user-initiated refunds with QRIS/VA: show bank account details in the table or a details modal
- Add "Admin Notes" field to the refund confirmation modal
- Show refund breakdown in refund details

### 2.3 Update RefundController

**File:** `Backend/app/Http/Controllers/Payment/RefundController.php`

- Fix the `store()` method to **update** existing pending refund instead of creating a duplicate
- Add `admin_notes`, `processed_by`, `processed_at` when admin confirms refund
- Add a `show()` method for viewing refund details

### 2.4 Update PaymentController cancel flow

**File:** `Backend/app/Http/Controllers/Payment/PaymentController.php`

- Use `RefundCalculationService` in the `cancel()` method to auto-calculate refund amounts
- Store breakdown in `room_refund`, `deposit_refund`, `other_refund` columns
- Set `refund_type = 'admin'` for admin-initiated cancellations

---

## Phase 3: Frontend API (Cancel API Endpoint)

### 3.1 Cancel booking API endpoint

**File:** `Frontend API/web-laravel-ulinmahoni/app/Http/Controllers/Api/BookingController.php`

Add new method `cancelBooking($orderId)`:
1. Validate user owns the booking (`user_id` matches authenticated user)
2. Validate booking is cancellable (`paid` and not checked-in, OR `pending`/`waiting`)
3. For `paid` bookings:
   - Use `RefundCalculationService` to calculate refund
   - If QRIS/VA payment: require `bank_name`, `account_no`, `account_holder` in request
   - Create `t_refund` record with `refund_type = 'user'`, `requested_by = user_id`, breakdown amounts, bank details
   - Set `transaction_status = 'cancelled'`, `cancel_at = now()`
   - Set booking `status = 0`
   - Release room (`rental_status = 0`) — check for other active bookings first
   - Release parking quota
   - Send FCM notification to user + admins
4. For `pending`/`waiting` bookings:
   - Set `transaction_status = 'cancelled'`, `cancel_at = now()`
   - Set booking `status = 0`
   - Release room and parking
   - No refund record needed

**Return response:**
```json
{
    "status": "success",
    "message": "Booking cancelled successfully",
    "data": {
        "order_id": "...",
        "refund": {
            "room_refund": 750000,
            "deposit_refund": 500000,
            "other_refund": 100000,
            "total_refund": 1350000,
            "refund_percentage": 75,
            "days_before_checkin": 12,
            "requires_bank_account": false
        }
    }
}
```

### 3.2 Refund preview API endpoint

**File:** Same controller

Add `previewCancelRefund($orderId)` — GET endpoint that returns the refund calculation **without** actually cancelling. This lets the UI show the user what they'll get back before they confirm.

### 3.3 Add routes

**File:** `Frontend API/web-laravel-ulinmahoni/routes/api.php`

```php
Route::get('/booking/{order_id}/cancel-preview', [BookingController::class, 'previewCancelRefund']);
Route::post('/booking/{order_id}/cancel', [BookingController::class, 'cancelBooking']);
```

### 3.4 Copy RefundCalculationService to Frontend API

**File:** New `Frontend API/web-laravel-ulinmahoni/app/Services/RefundCalculationService.php`

Same logic as Backend service (both apps share the same DB but are separate Laravel apps).

### 3.5 Add cancellation notification template

**File:** `Frontend API/web-laravel-ulinmahoni/app/Http/Controllers/Api/NotificationController.php`

Add `booking_cancelled` to the predefined notification types and templates array. Send to both guest and property admins.

### 3.6 Web portal cancel UI

**File:** `Frontend API/web-laravel-ulinmahoni/resources/views/bookings/index.blade.php`

Add cancel functionality to the My Bookings page:

- **For paid bookings (Completed tab):** Add "Cancel Booking" button in the Actions column (alongside Renew), visible when booking is `paid` and not checked-in
- **For pending/waiting bookings (Upcoming tab):** Add "Cancel" button in a new Actions column

**Cancel flow (Alpine.js modal):**
1. User clicks "Cancel Booking"
2. Modal opens → calls preview API → shows refund breakdown:
   - Room price refund: Rp X (Y%)
   - Deposit refund: Rp X (100%)
   - Parking refund: Rp X
   - **Total refund: Rp X**
   - Days before check-in: N
3. If QRIS/VA payment: show bank account form fields (bank name dropdown, account number, holder name)
4. User confirms → calls cancel API → show success/error alert
5. Refresh booking list

### 3.7 Web portal cancel routes

**File:** `Frontend API/web-laravel-ulinmahoni/routes/web.php`

Add web route for cancel that calls the API internally (or use JavaScript fetch to the API directly from the Alpine.js modal — preferred approach, consistent with existing patterns like attachment upload).

---

## Phase 4: Mobile App

### 4.1 Cancel/refund repository

**File:** New `Mobile App/lib/features/mybooking/mybookingdetails/data/repositories/cancel_booking_repository.dart`

Two methods:
- `previewCancelRefund(String orderId)` → GET `/booking/{order_id}/cancel-preview`
- `cancelBooking(String orderId, {String? bankName, String? accountNo, String? accountHolder})` → POST `/booking/{order_id}/cancel`

### 4.2 Cancel refund model

**File:** New `Mobile App/lib/features/mybooking/mybookingdetails/model/cancel_refund_model.dart`

```dart
class CancelRefundPreview {
  final double roomRefund;
  final double depositRefund;
  final double otherRefund;
  final double totalRefund;
  final int refundPercentage;
  final int daysBeforeCheckin;
  final bool requiresBankAccount;
}
```

### 4.3 Cancel booking provider

**File:** New `Mobile App/lib/features/mybooking/mybookingdetails/provider/cancel_booking_provider.dart`

Riverpod providers for preview and cancel operations (follow existing patterns like `renew_booking_provider.dart`).

### 4.4 Cancel booking dialog

**File:** New `Mobile App/lib/features/mybooking/mybookingdetails/presentation/widgets/cancel_booking_dialog.dart`

Dialog flow:
1. Show loading while fetching refund preview
2. Display refund breakdown (room, deposit, parking, total)
3. Show cancellation policy summary
4. If QRIS/VA: show bank account input fields
5. Confirm/Cancel buttons
6. On confirm: call cancel API, show success, pop back to My Bookings list

### 4.5 Add cancel button to detail page

**File:** `Mobile App/lib/features/mybooking/mybookingdetails/presentation/pages/mybookingdetails_page.dart`

Add a new `Builder` section (following the existing pattern of check-in and renew sections):

- **For `paid` + not checked-in bookings:** Show "Cancel Booking" button (red) between Payment section and Check-In section
- **For `pending`/`waiting` bookings:** Show "Cancel Booking" button below the payment info section
- Button opens the `CancelBookingDialog`

### 4.6 Update mybooking list to reflect cancellations

**File:** `Mobile App/lib/features/mybooking/mybooking/controller/mybooking_controller.dart`

Ensure cancelled bookings move to the "Completed" tab (already handled — `cancelled` is in the completed filter).

---

## Phase 5: Backend Admin Enhancements

### 5.1 Distinguish user vs admin refunds

**File:** `Backend/resources/views/pages/payment/refund/index.blade.php`

- Add "Source" column showing "User Request" or "Admin" badge
- For user requests: show bank account details in a collapsible row or modal
- Add filter for refund source (user/admin)

---

## Files to Modify (Summary)

### Backend
| File | Change |
|------|--------|
| `database/migrations/new_migration.php` | New: add columns to t_refund |
| `app/Models/Refund.php` | Update $fillable, add relationships |
| `app/Services/RefundCalculationService.php` | New: refund calculation logic |
| `app/Http/Controllers/Payment/PaymentController.php` | Use RefundCalculationService |
| `app/Http/Controllers/Payment/RefundController.php` | Fix duplicate bug, add notes/processed fields |
| `resources/views/pages/payment/refund/index.blade.php` | Add user refund details, source column |

### Frontend API
| File | Change |
|------|--------|
| `app/Services/RefundCalculationService.php` | New: refund calculation logic |
| `app/Http/Controllers/Api/BookingController.php` | Add cancelBooking(), previewCancelRefund() |
| `app/Http/Controllers/Api/NotificationController.php` | Add booking_cancelled template |
| `routes/api.php` | Add cancel/preview routes |
| `resources/views/bookings/index.blade.php` | Add cancel button + modal |

### Mobile App
| File | Change |
|------|--------|
| `lib/features/mybooking/mybookingdetails/data/repositories/cancel_booking_repository.dart` | New |
| `lib/features/mybooking/mybookingdetails/model/cancel_refund_model.dart` | New |
| `lib/features/mybooking/mybookingdetails/provider/cancel_booking_provider.dart` | New |
| `lib/features/mybooking/mybookingdetails/presentation/widgets/cancel_booking_dialog.dart` | New |
| `lib/features/mybooking/mybookingdetails/presentation/pages/mybookingdetails_page.dart` | Add cancel section |

### Project Root
| File | Change |
|------|--------|
| `whatsnew.md` | Add entries for all three apps |

---

## Verification

1. **Backend migration:** Run `php artisan migrate` locally — verify new columns exist on `t_refund`
2. **Refund calculation:** Test with various days-before-checkin values (15, 9, 5, 1) and verify correct percentages
3. **API preview endpoint:** `GET /api/v1/booking/{order_id}/cancel-preview` returns correct breakdown
4. **API cancel endpoint:** `POST /api/v1/booking/{order_id}/cancel` for paid booking → verify:
   - Transaction status changed to `cancelled`
   - Booking status set to 0
   - Room `rental_status` set to 0 (if no other active bookings)
   - Parking quota released
   - Refund record created with correct breakdown
   - FCM notification sent
5. **API cancel for pending:** Cancel a pending booking → no refund record, just status change
6. **Web portal:** Test cancel flow on My Bookings page — preview modal, bank account form for VA/QRIS, confirm
7. **Mobile app:** Test cancel flow on booking detail page — preview dialog, bank fields, confirm, navigation back
8. **Backend admin:** Verify user-initiated refunds appear on refund page with bank details and source badge
9. **Dark mode:** Verify cancel modal/dialog looks correct in dark mode on all three apps

## Implementation Order

1. Database migration (Phase 1)
2. RefundCalculationService in both Backend and Frontend API (Phase 2.1, 3.4)
3. Cancel API endpoints (Phase 3.1–3.3, 3.5)
4. Backend admin improvements (Phase 2.2–2.4, 5.1)
5. Web portal cancel UI (Phase 3.6)
6. Mobile App cancel feature (Phase 4)
7. Update whatsnew.md
