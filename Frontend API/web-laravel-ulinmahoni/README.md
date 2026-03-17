# Ulin Mahoni — Property Rental Booking Platform

A Laravel-based property rental management system supporting daily and monthly room rentals, payment processing, and multi-language support (Indonesian / English).

---

## Booking Flow

1. **Browse & Select** — User picks a property and room, selects dates and rental type (daily / monthly).
2. **Create Booking** — A transaction is created with status `pending`. It expires automatically if unpaid within the deadline.
3. **Choose Payment Method** — User selects a payment method (bank transfer, virtual account, or QRIS).
4. **Upload Payment Proof** — After transferring, the user uploads a JPEG or PNG image as proof of payment. Status moves to `waiting`.
5. **Re-upload** — If the wrong file was uploaded, the user can replace it while the booking is still under review.
6. **Admin Review** — Admin confirms payment; status moves to `paid` / `completed`.
7. **Cancellation / Expiry** — Bookings not paid in time are automatically expired. Users can cancel eligible bookings.

---

## Transaction Statuses

| Status | Meaning |
|---|---|
| `pending` | Booking created, awaiting payment |
| `waiting` | Payment proof uploaded, awaiting admin confirmation |
| `paid` | Payment confirmed |
| `completed` | Stay completed |
| `cancelled` | Cancelled by user or admin |
| `expired` | Not paid within the allowed window |

---

## Payment Proof (Attachment)

- Accepted formats: JPEG, PNG
- Maximum size: 10 MB
- Can be replaced at any time while the booking is in `waiting` status
- Viewable via a time-limited signed URL (valid 10 minutes)

---

## Key Booking Endpoints

| Method | URI | Description |
|---|---|---|
| GET | `/bookings` | List user bookings with status tabs |
| POST | `/bookings` | Create a new booking |
| POST | `/bookings/{id}/upload-attachment` | Upload or re-upload payment proof |
| GET | `/bookings/{id}/attachment` | View attachment (signed URL) |
| POST | `/bookings/{id}/payment-method` | Update payment method |
| GET | `/payment/{booking}` | Payment page |

---

## Supported Payment Methods

Processed via the **DOKU** payment gateway:

- Virtual accounts — BRI, BNI, BCA, Mandiri, BSI, Permata, Maybank, CIMB, Danamon, BNC
- QRIS

---

## Rental Types

| Type | Pricing Unit | Checkout Date |
|---|---|---|
| Daily | Per night | User-selected |
| Monthly | Per month | Auto-calculated from check-in + months |
