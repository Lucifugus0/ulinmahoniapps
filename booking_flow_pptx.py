#!/usr/bin/env python3
"""Generate Ulin Mahoni Booking Flow PPTX presentation."""

from pptx import Presentation
from pptx.util import Inches, Pt, Emu
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.enum.shapes import MSO_SHAPE

# Color palette
DARK_BG = RGBColor(0x0F, 0x17, 0x2A)
TEAL = RGBColor(0x2D, 0xD4, 0xBF)
WHITE = RGBColor(0xFF, 0xFF, 0xFF)
LIGHT_GRAY = RGBColor(0xA0, 0xA0, 0xA0)
DARK_CARD = RGBColor(0x1E, 0x29, 0x3B)
GREEN = RGBColor(0x10, 0xB9, 0x81)
RED = RGBColor(0xEF, 0x44, 0x44)
YELLOW = RGBColor(0xFB, 0xBF, 0x24)
BLUE = RGBColor(0x38, 0x9C, 0xFC)
ORANGE = RGBColor(0xF9, 0x73, 0x16)
PURPLE = RGBColor(0xA7, 0x8B, 0xFA)
PINK = RGBColor(0xEC, 0x48, 0x99)

prs = Presentation()
prs.slide_width = Inches(13.333)
prs.slide_height = Inches(7.5)

def set_slide_bg(slide, color):
    bg = slide.background
    fill = bg.fill
    fill.solid()
    fill.fore_color.rgb = color

def add_text_box(slide, left, top, width, height, text, font_size=14,
                 color=WHITE, bold=False, alignment=PP_ALIGN.LEFT, font_name='Calibri'):
    txBox = slide.shapes.add_textbox(Inches(left), Inches(top), Inches(width), Inches(height))
    tf = txBox.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.text = text
    p.font.size = Pt(font_size)
    p.font.color.rgb = color
    p.font.bold = bold
    p.font.name = font_name
    p.alignment = alignment
    return txBox

def add_rounded_rect(slide, left, top, width, height, fill_color, text='',
                     font_size=11, font_color=WHITE, bold=False, alignment=PP_ALIGN.CENTER):
    shape = slide.shapes.add_shape(
        MSO_SHAPE.ROUNDED_RECTANGLE,
        Inches(left), Inches(top), Inches(width), Inches(height)
    )
    shape.fill.solid()
    shape.fill.fore_color.rgb = fill_color
    shape.line.fill.background()
    if text:
        tf = shape.text_frame
        tf.word_wrap = True
        tf.paragraphs[0].alignment = alignment
        p = tf.paragraphs[0]
        p.text = text
        p.font.size = Pt(font_size)
        p.font.color.rgb = font_color
        p.font.bold = bold
        p.font.name = 'Calibri'
    return shape

def add_multi_text_box(slide, left, top, width, height, lines, default_size=12, default_color=WHITE):
    """Add a text box with multiple formatted lines. Each line is (text, size, color, bold)."""
    txBox = slide.shapes.add_textbox(Inches(left), Inches(top), Inches(width), Inches(height))
    tf = txBox.text_frame
    tf.word_wrap = True
    for i, line_data in enumerate(lines):
        text = line_data[0]
        size = line_data[1] if len(line_data) > 1 else default_size
        color = line_data[2] if len(line_data) > 2 else default_color
        bold = line_data[3] if len(line_data) > 3 else False
        if i == 0:
            p = tf.paragraphs[0]
        else:
            p = tf.add_paragraph()
        p.text = text
        p.font.size = Pt(size)
        p.font.color.rgb = color
        p.font.bold = bold
        p.font.name = 'Calibri'
        p.space_after = Pt(4)
    return txBox

def add_arrow(slide, left, top, width=0.6, color=TEAL):
    """Add a right-pointing arrow."""
    shape = slide.shapes.add_shape(
        MSO_SHAPE.RIGHT_ARROW,
        Inches(left), Inches(top), Inches(width), Inches(0.3)
    )
    shape.fill.solid()
    shape.fill.fore_color.rgb = color
    shape.line.fill.background()
    return shape

def add_down_arrow(slide, left, top, height=0.4, color=TEAL):
    shape = slide.shapes.add_shape(
        MSO_SHAPE.DOWN_ARROW,
        Inches(left), Inches(top), Inches(0.3), Inches(height)
    )
    shape.fill.solid()
    shape.fill.fore_color.rgb = color
    shape.line.fill.background()
    return shape

def add_availability_badge(slide, left, top, status, room_type):
    """Add room availability badge."""
    if status == 'available':
        color = GREEN
        text = 'AVAILABLE'
    elif status == 'blocked':
        color = YELLOW
        text = 'RESERVED'
    elif status == 'occupied':
        color = RED
        text = 'OCCUPIED'
    else:
        color = LIGHT_GRAY
        text = status.upper()

    add_rounded_rect(slide, left, top, 1.2, 0.3, color, text, 8, WHITE, True)
    add_text_box(slide, left + 1.3, top, 1.0, 0.3, room_type, 8, LIGHT_GRAY)


# ============================================================
# SLIDE 1: Title
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])  # Blank
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 1, 1.5, 11, 1, 'ULIN MAHONI', 48, TEAL, True, PP_ALIGN.CENTER)
add_text_box(slide, 1, 2.5, 11, 1, 'Booking Flow & Room Availability', 32, WHITE, False, PP_ALIGN.CENTER)
add_text_box(slide, 1, 3.5, 11, 0.5, 'Daily & Monthly Rental  |  Indonesia Kos Platform', 18, LIGHT_GRAY, False, PP_ALIGN.CENTER)

# Flow overview boxes
steps = ['Search', 'Book', 'Pay', 'Check-In', 'Renewal', 'Check-Out', 'Refund']
colors = [BLUE, TEAL, GREEN, PURPLE, ORANGE, PINK, RED]
x_start = 1.5
for i, (step, clr) in enumerate(zip(steps, colors)):
    add_rounded_rect(slide, x_start + i * 1.5, 5.2, 1.3, 0.5, clr, step, 12, WHITE, True)
    if i < len(steps) - 1:
        add_arrow(slide, x_start + i * 1.5 + 1.3, 5.3, 0.2, LIGHT_GRAY)

add_text_box(slide, 1, 6.5, 11, 0.4, 'Complete lifecycle from room search to deposit refund', 14, LIGHT_GRAY, False, PP_ALIGN.CENTER)


# ============================================================
# SLIDE 2: Overview — Daily vs Monthly
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 12, 0.6, 'Daily vs Monthly Rental Overview', 28, TEAL, True)

# Daily column
add_rounded_rect(slide, 0.5, 1.2, 5.8, 5.5, DARK_CARD)
add_text_box(slide, 0.8, 1.3, 5, 0.5, 'DAILY RENTAL', 22, BLUE, True)
add_multi_text_box(slide, 0.8, 1.9, 5.2, 4.5, [
    ('Booking Constraints', 14, TEAL, True),
    ('  Max check-in date: 90 days from today', 13, WHITE),
    ('  Max stay duration: 60 days', 13, WHITE),
    ('  Check-in time: 14:00  |  Check-out time: 12:00', 13, WHITE),
    ('', 8),
    ('Room Availability', 14, TEAL, True),
    ('  Rooms are ALWAYS shown in search results', 13, WHITE),
    ('  rental_status flag is IGNORED for daily rooms', 13, WHITE),
    ('  Availability checked by date conflict with existing bookings', 13, WHITE),
    ('  Same-day turnaround allowed (12:00 out, 14:00 in)', 13, WHITE),
    ('', 8),
    ('Pricing', 14, TEAL, True),
    ('  5 pricing tiers per date:', 13, WHITE),
    ('  High Season > Low Season > Holiday > Weekend > Weekday', 13, YELLOW),
    ('  Per-date pricing stored in m_room_prices', 13, WHITE),
    ('  Manual price overrides preserved', 13, WHITE),
])

# Monthly column
add_rounded_rect(slide, 6.8, 1.2, 5.8, 5.5, DARK_CARD)
add_text_box(slide, 7.1, 1.3, 5, 0.5, 'MONTHLY RENTAL', 22, PURPLE, True)
add_multi_text_box(slide, 7.1, 1.9, 5.2, 4.5, [
    ('Booking Constraints', 14, TEAL, True),
    ('  Max check-in date: 14 days from today', 13, WHITE),
    ('  Max stay duration: 12 months', 13, WHITE),
    ('  Check-in date: flexible  |  Check-out: same day next month(s)', 13, WHITE),
    ('', 8),
    ('Room Availability', 14, TEAL, True),
    ('  Controlled by rental_status flag', 13, WHITE),
    ('  rental_status = 0 : Available (shown in search)', 13, GREEN),
    ('  rental_status = 1 : Booked (hidden from search)', 13, RED),
    ('  Status blocked immediately on booking creation', 13, WHITE),
    ('', 8),
    ('Pricing', 14, TEAL, True),
    ('  Flat monthly price from room record', 13, WHITE),
    ('  Price locked at booking time', 13, WHITE),
    ('  Renewal uses current pricing (may differ)', 13, YELLOW),
    ('  original_checkin_day preserved across renewals', 13, WHITE),
])


# ============================================================
# SLIDE 3: Step 1 — Search Room
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 3, 0.6, 'Step 1', 16, LIGHT_GRAY, False)
add_text_box(slide, 1.5, 0.3, 10, 0.6, 'Search Room', 28, BLUE, True)

# User action
add_rounded_rect(slide, 0.5, 1.2, 6, 2.5, DARK_CARD)
add_text_box(slide, 0.8, 1.3, 5, 0.4, 'USER ACTIONS', 16, TEAL, True)
add_multi_text_box(slide, 0.8, 1.8, 5.5, 2.0, [
    ('1. Select property (or browse all)', 13, WHITE),
    ('2. Choose rental period: Daily or Monthly', 13, WHITE),
    ('3. Select check-in and check-out dates', 13, WHITE),
    ('4. System filters available rooms', 13, WHITE),
    ('5. View room details, photos, facilities, pricing', 13, WHITE),
])

# System logic
add_rounded_rect(slide, 7, 1.2, 5.8, 2.5, DARK_CARD)
add_text_box(slide, 7.3, 1.3, 5, 0.4, 'SYSTEM LOGIC', 16, TEAL, True)
add_multi_text_box(slide, 7.3, 1.8, 5.2, 2.0, [
    ('Daily: Show all active rooms (status=1)', 13, BLUE),
    ('  → Exclude rooms with conflicting date bookings', 12, LIGHT_GRAY),
    ('  → Check-in 14:00 / Check-out 12:00 boundary', 12, LIGHT_GRAY),
    ('Monthly: Filter by rental_status = 0', 13, PURPLE),
    ('  → Only show rooms not currently occupied', 12, LIGHT_GRAY),
    ('  → Immediate block on any booking creation', 12, LIGHT_GRAY),
])

# Room availability section
add_text_box(slide, 0.5, 4.0, 12, 0.5, 'Room Availability at This Step', 18, TEAL, True)

# Daily example
add_rounded_rect(slide, 0.5, 4.6, 6, 2.5, DARK_CARD)
add_text_box(slide, 0.8, 4.7, 5, 0.4, 'DAILY ROOMS', 14, BLUE, True)
rooms_daily = [
    ('Room 101', 'available', 'Apr 10-12 free'),
    ('Room 102', 'available', 'Apr 10-12 free'),
    ('Room 103', 'occupied', 'Apr 10-11 booked by another guest'),
    ('Room 104', 'available', 'Apr 10-12 free'),
]
for i, (name, status, note) in enumerate(rooms_daily):
    y = 5.2 + i * 0.4
    add_text_box(slide, 0.8, y, 1.2, 0.3, name, 11, WHITE, True)
    clr = GREEN if status == 'available' else RED
    lbl = 'AVAILABLE' if status == 'available' else 'CONFLICT'
    add_rounded_rect(slide, 2.2, y, 1.1, 0.3, clr, lbl, 9, WHITE, True)
    add_text_box(slide, 3.5, y, 3, 0.3, note, 10, LIGHT_GRAY)

# Monthly example
add_rounded_rect(slide, 7, 4.6, 5.8, 2.5, DARK_CARD)
add_text_box(slide, 7.3, 4.7, 5, 0.4, 'MONTHLY ROOMS', 14, PURPLE, True)
rooms_monthly = [
    ('Room 201', 'available', 'rental_status = 0'),
    ('Room 202', 'occupied', 'rental_status = 1 (occupied)'),
    ('Room 203', 'available', 'rental_status = 0'),
    ('Room 204', 'occupied', 'rental_status = 1 (occupied)'),
]
for i, (name, status, note) in enumerate(rooms_monthly):
    y = 5.2 + i * 0.4
    add_text_box(slide, 7.3, y, 1.2, 0.3, name, 11, WHITE, True)
    clr = GREEN if status == 'available' else RED
    lbl = 'AVAILABLE' if status == 'available' else 'OCCUPIED'
    add_rounded_rect(slide, 8.7, y, 1.1, 0.3, clr, lbl, 9, WHITE, True)
    add_text_box(slide, 10.0, y, 2.5, 0.3, note, 10, LIGHT_GRAY)


# ============================================================
# SLIDE 4: Step 2 — Make Booking
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 3, 0.6, 'Step 2', 16, LIGHT_GRAY, False)
add_text_box(slide, 1.5, 0.3, 10, 0.6, 'Make Booking', 28, TEAL, True)

# Flow
add_rounded_rect(slide, 0.5, 1.2, 3.8, 1.8, DARK_CARD)
add_multi_text_box(slide, 0.7, 1.3, 3.4, 1.6, [
    ('User Submits Booking', 14, TEAL, True),
    ('POST /api/v1/booking', 11, LIGHT_GRAY),
    ('', 6),
    ('Selects room, dates, parking', 12, WHITE),
    ('Reviews pricing breakdown', 12, WHITE),
    ('Confirms booking request', 12, WHITE),
])

add_arrow(slide, 4.3, 1.9, 0.4, TEAL)

add_rounded_rect(slide, 4.9, 1.2, 3.8, 1.8, DARK_CARD)
add_multi_text_box(slide, 5.1, 1.3, 3.4, 1.6, [
    ('System Creates Records', 14, TEAL, True),
    ('', 6),
    ('Transaction: status = pending', 12, YELLOW),
    ('Booking: status = 1 (active)', 12, GREEN),
    ('Payment: status = unpaid', 12, RED),
    ('Expiry: 15 min countdown', 12, ORANGE, True),
])

add_arrow(slide, 8.7, 1.9, 0.4, TEAL)

add_rounded_rect(slide, 9.3, 1.2, 3.5, 1.8, DARK_CARD)
add_multi_text_box(slide, 9.5, 1.3, 3.1, 1.6, [
    ('If Expired (15 min)', 14, RED, True),
    ('', 6),
    ('Transaction: status = expired', 12, RED),
    ('Booking: status = 0 (inactive)', 12, RED),
    ('Monthly: rental_status = 0', 12, GREEN),
    ('Room released back to pool', 12, GREEN),
])

# Room availability impact
add_text_box(slide, 0.5, 3.3, 12, 0.5, 'Room Availability Impact', 18, TEAL, True)

add_rounded_rect(slide, 0.5, 3.9, 6, 3.0, DARK_CARD)
add_text_box(slide, 0.8, 4.0, 5, 0.4, 'DAILY ROOM BOOKED', 14, BLUE, True)
add_multi_text_box(slide, 0.8, 4.5, 5.5, 2.2, [
    ('Room 101 booked for Apr 10-12', 13, WHITE),
    ('', 6),
    ('rental_status: NO CHANGE (stays 0)', 13, GREEN, True),
    ('', 4),
    ('Other users searching Apr 10-12:', 12, LIGHT_GRAY),
    ('  Room 101: hidden (date conflict detected)', 12, RED),
    ('  Room 102: still available', 12, GREEN),
    ('', 4),
    ('Other users searching Apr 13-15:', 12, LIGHT_GRAY),
    ('  Room 101: available (no conflict)', 12, GREEN),
])

add_rounded_rect(slide, 7, 3.9, 5.8, 3.0, DARK_CARD)
add_text_box(slide, 7.3, 4.0, 5, 0.4, 'MONTHLY ROOM BOOKED', 14, PURPLE, True)
add_multi_text_box(slide, 7.3, 4.5, 5.2, 2.2, [
    ('Room 201 booked for Apr-May', 13, WHITE),
    ('', 6),
    ('rental_status: IMMEDIATELY SET TO 1', 13, RED, True),
    ('', 4),
    ('Other users searching any dates:', 12, LIGHT_GRAY),
    ('  Room 201: hidden from all searches', 12, RED),
    ('  Room 203: still available (status=0)', 12, GREEN),
    ('', 4),
    ('Room blocked even during 15-min', 12, YELLOW),
    ('payment window to prevent double-booking', 12, YELLOW),
])


# ============================================================
# SLIDE 5: Step 3 — Payment
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 3, 0.6, 'Step 3', 16, LIGHT_GRAY, False)
add_text_box(slide, 1.5, 0.3, 10, 0.6, 'Make Payment', 28, GREEN, True)

# Payment methods
add_rounded_rect(slide, 0.5, 1.2, 4.0, 3.5, DARK_CARD)
add_text_box(slide, 0.8, 1.3, 3.5, 0.4, 'PAYMENT METHODS', 16, TEAL, True)
add_multi_text_box(slide, 0.8, 1.8, 3.5, 2.8, [
    ('Virtual Account (VA)', 14, BLUE, True),
    ('  BRI, BNI, BCA, Mandiri, CIMB', 12, WHITE),
    ('  Unique VA number generated per booking', 11, LIGHT_GRAY),
    ('', 6),
    ('QRIS', 14, GREEN, True),
    ('  Scan QR code to pay', 12, WHITE),
    ('  Supported by all Indonesian banks', 11, LIGHT_GRAY),
    ('', 6),
    ('Credit Card', 14, PURPLE, True),
    ('  Direct card payment via DOKU', 12, WHITE),
    ('', 6),
    ('Powered by DOKU Payment Gateway', 11, YELLOW),
])

# Payment flow
add_rounded_rect(slide, 4.8, 1.2, 4.0, 3.5, DARK_CARD)
add_text_box(slide, 5.1, 1.3, 3.5, 0.4, 'PAYMENT FLOW', 16, TEAL, True)
add_multi_text_box(slide, 5.1, 1.8, 3.5, 2.8, [
    ('1. User selects payment method', 13, WHITE),
    ('2. System generates payment code/QR', 13, WHITE),
    ('3. User completes payment', 13, WHITE),
    ('4. DOKU sends webhook callback', 13, WHITE),
    ('5. System verifies & updates status', 13, WHITE),
    ('', 8),
    ('Status Transition:', 13, TEAL, True),
    ('  pending  →  paid', 13, GREEN, True),
    ('', 6),
    ('Payment deadline: 15 minutes', 12, ORANGE),
    ('Auto-expire if not paid in time', 12, ORANGE),
    ('Admin can manually verify payment', 12, LIGHT_GRAY),
])

# After payment
add_rounded_rect(slide, 9.1, 1.2, 3.7, 3.5, DARK_CARD)
add_text_box(slide, 9.4, 1.3, 3.2, 0.4, 'AFTER PAYMENT', 16, TEAL, True)
add_multi_text_box(slide, 9.4, 1.8, 3.2, 2.8, [
    ('Transaction:', 13, WHITE, True),
    ('  status = paid', 13, GREEN, True),
    ('', 6),
    ('Booking:', 13, WHITE, True),
    ('  status = 1 (active)', 13, GREEN),
    ('  Waiting for check-in', 13, YELLOW),
    ('', 6),
    ('Push Notification:', 13, WHITE, True),
    ('  Sent to guest + admins', 13, TEAL),
    ('', 6),
    ('Room Availability:', 13, WHITE, True),
    ('  Daily: unchanged', 13, BLUE),
    ('  Monthly: stays blocked', 13, PURPLE),
])

# Room availability
add_text_box(slide, 0.5, 5.0, 12, 0.5, 'Room Availability: No Change from Step 2', 16, TEAL, True)
add_multi_text_box(slide, 0.5, 5.5, 12, 1.5, [
    ('Daily rooms: Still available for non-conflicting dates. Conflict-based filtering continues.', 13, BLUE),
    ('Monthly rooms: rental_status remains 1. Room stays hidden from all searches.', 13, PURPLE),
    ('Payment confirmation does not change room availability — it was already reserved at booking creation.', 13, LIGHT_GRAY),
])


# ============================================================
# SLIDE 6: Step 4 — Check-In
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 3, 0.6, 'Step 4', 16, LIGHT_GRAY, False)
add_text_box(slide, 1.5, 0.3, 10, 0.6, 'Check-In', 28, PURPLE, True)

# Admin process
add_rounded_rect(slide, 0.5, 1.2, 6, 3.0, DARK_CARD)
add_text_box(slide, 0.8, 1.3, 5, 0.4, 'ADMIN CHECK-IN PROCESS', 16, TEAL, True)
add_multi_text_box(slide, 0.8, 1.8, 5.5, 2.3, [
    ('1. Admin opens Confirmed Bookings page', 13, WHITE),
    ('2. Clicks Check-In button on the booking', 13, WHITE),
    ('3. Uploads guest ID document:', 13, WHITE),
    ('   KTP / Passport / SIM / Other', 12, LIGHT_GRAY),
    ('4. Verifies guest identity (NIK, photo)', 13, WHITE),
    ('5. System records:', 13, WHITE),
    ('   check_in_at = current timestamp', 12, TEAL),
    ('   checked_in_by = admin user ID', 12, TEAL),
    ('6. Prints registration form & invoice', 13, WHITE),
])

# Status changes
add_rounded_rect(slide, 7, 1.2, 5.8, 3.0, DARK_CARD)
add_text_box(slide, 7.3, 1.3, 5, 0.4, 'STATUS CHANGES', 16, TEAL, True)
add_multi_text_box(slide, 7.3, 1.8, 5.2, 2.3, [
    ('Before Check-In:', 14, YELLOW, True),
    ('  Transaction: paid', 12, GREEN),
    ('  Booking: status = 1, check_in_at = NULL', 12, WHITE),
    ('  State: "Waiting for Check-In"', 12, YELLOW),
    ('', 8),
    ('After Check-In:', 14, GREEN, True),
    ('  Transaction: paid (unchanged)', 12, GREEN),
    ('  Booking: check_in_at = timestamp', 12, TEAL),
    ('  State: "Checked-In"', 12, GREEN),
    ('  check_out_at = NULL (awaiting checkout)', 12, WHITE),
])

# Room availability
add_text_box(slide, 0.5, 4.5, 12, 0.5, 'Room Availability at This Step', 18, TEAL, True)
add_rounded_rect(slide, 0.5, 5.1, 12.3, 2.0, DARK_CARD)
add_multi_text_box(slide, 0.8, 5.2, 11.8, 1.8, [
    ('Daily Rooms: NO CHANGE — room availability still determined by date conflicts only', 14, BLUE),
    ('Monthly Rooms: NO CHANGE — rental_status remains 1 (was set at booking creation)', 14, PURPLE),
    ('', 8),
    ('The physical check-in does not change any availability flags.', 13, WHITE),
    ('Room was already reserved/blocked since Step 2 (booking creation).', 13, LIGHT_GRAY),
    ('Guest is now physically occupying the room.', 13, LIGHT_GRAY),
])


# ============================================================
# SLIDE 7: Step 5 — Renewal
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 3, 0.6, 'Step 5', 16, LIGHT_GRAY, False)
add_text_box(slide, 1.5, 0.3, 10, 0.6, 'Renewal (Extend Stay)', 28, ORANGE, True)

# Renewal process
add_rounded_rect(slide, 0.5, 1.1, 6, 3.2, DARK_CARD)
add_text_box(slide, 0.8, 1.2, 5, 0.4, 'RENEWAL PROCESS', 16, TEAL, True)
add_multi_text_box(slide, 0.8, 1.7, 5.5, 2.8, [
    ('Trigger: Guest wants to extend stay', 13, WHITE),
    ('POST /api/v1/booking/{order_id}/renew', 11, LIGHT_GRAY),
    ('', 6),
    ('1. Validate original booking is paid', 13, WHITE),
    ('2. Skip rental_status check (same tenant)', 13, YELLOW),
    ('3. Create NEW transaction (is_renewal = 1)', 13, WHITE),
    ('4. Create NEW booking record', 13, WHITE),
    ('5. New payment record (unpaid, 15 min)', 13, WHITE),
    ('', 6),
    ('Original Booking Update:', 13, TEAL, True),
    ('  check_out_at = now() (logged for records)', 12, WHITE),
    ('  renewal_status = 1 (flagged as renewed)', 12, YELLOW),
    ('  Parking quota NOT incremented', 12, ORANGE),
])

# Continuity
add_rounded_rect(slide, 7, 1.1, 5.8, 3.2, DARK_CARD)
add_text_box(slide, 7.3, 1.2, 5, 0.4, 'CONTINUOUS OCCUPANCY', 16, TEAL, True)
add_multi_text_box(slide, 7.3, 1.7, 5.2, 2.8, [
    ('Key Principle:', 14, ORANGE, True),
    ('Room NEVER becomes available between', 13, WHITE),
    ('original and renewal booking', 13, WHITE),
    ('', 6),
    ('Monthly Renewal Chain:', 13, PURPLE, True),
    ('  Jan 31 check-in → Renew to Feb', 12, WHITE),
    ('  original_checkin_day = 31 preserved', 12, YELLOW),
    ('  Next renewal: Mar 31 (not Feb 28)', 12, WHITE),
    ('  Prevents day-loss across months', 12, LIGHT_GRAY),
    ('', 6),
    ('Daily Renewal:', 13, BLUE, True),
    ('  Simply extends the date range', 12, WHITE),
    ('  New conflict check for extended dates', 12, WHITE),
    ('  Other bookings still respected', 12, LIGHT_GRAY),
])

# Room availability
add_text_box(slide, 0.5, 4.5, 12, 0.5, 'Room Availability at This Step', 18, TEAL, True)
add_rounded_rect(slide, 0.5, 5.1, 12.3, 2.0, DARK_CARD)
add_multi_text_box(slide, 0.8, 5.2, 11.8, 1.8, [
    ('Daily Rooms: Extended date range now included in conflict check. Room blocked for original + renewal dates.', 14, BLUE),
    ('Monthly Rooms: rental_status STAYS 1 — continuous occupancy, no gap. Room remains hidden from search.', 14, PURPLE),
    ('', 8),
    ('If renewal payment expires (15 min): Room reverts to original booking state.', 13, ORANGE),
    ('If renewal is cancelled: Original booking restored (renewal_status = 0, check_out_at = NULL).', 13, LIGHT_GRAY),
])


# ============================================================
# SLIDE 8: Step 6 — Check-Out
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 3, 0.6, 'Step 6', 16, LIGHT_GRAY, False)
add_text_box(slide, 1.5, 0.3, 10, 0.6, 'Check-Out', 28, PINK, True)

# Admin process
add_rounded_rect(slide, 0.5, 1.2, 6, 3.0, DARK_CARD)
add_text_box(slide, 0.8, 1.3, 5, 0.4, 'ADMIN CHECK-OUT PROCESS', 16, TEAL, True)
add_multi_text_box(slide, 0.8, 1.8, 5.5, 2.3, [
    ('1. Admin opens "Check-out Hari Ini" page', 13, WHITE),
    ('   Shows bookings due today or overdue', 12, LIGHT_GRAY),
    ('2. Opens checkout modal for the booking', 13, WHITE),
    ('3. Records room item conditions', 13, WHITE),
    ('   Logs any damages & additional charges', 12, LIGHT_GRAY),
    ('4. Adds checkout notes if needed', 13, WHITE),
    ('5. Confirms check-out', 13, WHITE),
    ('6. System records:', 13, WHITE),
    ('   check_out_at = current timestamp', 12, TEAL),
    ('   checked_out_by = admin user ID', 12, TEAL),
])

# Status changes
add_rounded_rect(slide, 7, 1.2, 5.8, 3.0, DARK_CARD)
add_text_box(slide, 7.3, 1.3, 5, 0.4, 'STATUS CHANGES', 16, TEAL, True)
add_multi_text_box(slide, 7.3, 1.8, 5.2, 2.3, [
    ('Booking:', 14, WHITE, True),
    ('  status = 0 (inactive)', 13, RED),
    ('  check_out_at = timestamp', 13, WHITE),
    ('', 6),
    ('Transaction:', 14, WHITE, True),
    ('  status = paid (unchanged)', 13, GREEN),
    ('', 6),
    ('Parking:', 14, WHITE, True),
    ('  Quota decremented (freed up)', 13, TEAL),
    ('  From all 3 sources checked', 13, LIGHT_GRAY),
])

# Room availability - THE BIG CHANGE
add_text_box(slide, 0.5, 4.5, 12, 0.5, 'Room Availability at This Step', 18, TEAL, True)
add_rounded_rect(slide, 0.5, 5.1, 6, 2.0, DARK_CARD)
add_text_box(slide, 0.8, 5.2, 5, 0.4, 'IF NO OTHER ACTIVE BOOKINGS', 14, GREEN, True)
add_multi_text_box(slide, 0.8, 5.7, 5.5, 1.3, [
    ('Daily: Room dates freed from conflict list', 13, BLUE),
    ('Monthly: rental_status = 0', 13, GREEN, True),
    ('Room returns to available pool', 13, GREEN),
    ('Visible in search results again', 13, WHITE),
])

add_rounded_rect(slide, 7, 5.1, 5.8, 2.0, DARK_CARD)
add_text_box(slide, 7.3, 5.2, 5, 0.4, 'IF OTHER ACTIVE BOOKINGS EXIST', 14, YELLOW, True)
add_multi_text_box(slide, 7.3, 5.7, 5.2, 1.3, [
    ('Next guest already has booking for this room', 13, WHITE),
    ('Monthly: rental_status stays 1', 13, RED),
    ('Room remains blocked for next occupant', 13, YELLOW),
    ('Seamless transition between tenants', 13, LIGHT_GRAY),
])


# ============================================================
# SLIDE 9: Step 7 — Deposit Refund
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 3, 0.6, 'Step 7', 16, LIGHT_GRAY, False)
add_text_box(slide, 1.5, 0.3, 10, 0.6, 'Refund & Deposit', 28, RED, True)

# Cancellation refund tiers
add_rounded_rect(slide, 0.5, 1.1, 5.5, 3.4, DARK_CARD)
add_text_box(slide, 0.8, 1.2, 5, 0.4, 'CANCELLATION REFUND TIERS', 16, TEAL, True)
add_multi_text_box(slide, 0.8, 1.7, 5.0, 2.8, [
    ('Days Before Check-In → Refund %', 13, WHITE, True),
    ('', 6),
    ('  > 10 days :  75% room + parking', 14, GREEN),
    ('  9-7 days  :  50% room + parking', 14, YELLOW),
    ('  6-3 days  :  25% room + parking', 14, ORANGE),
    ('  < 3 days  :   0% (no refund)', 14, RED),
    ('', 8),
    ('Deposit: ALWAYS 100% refunded', 14, GREEN, True),
    ('Service/admin fees: NOT refunded', 13, RED),
    ('Admin can override calculated amount', 12, LIGHT_GRAY),
])

# Refund process
add_rounded_rect(slide, 6.3, 1.1, 6.5, 3.4, DARK_CARD)
add_text_box(slide, 6.6, 1.2, 6, 0.4, 'REFUND PROCESS', 16, TEAL, True)
add_multi_text_box(slide, 6.6, 1.7, 6.0, 2.8, [
    ('1. User/Admin initiates cancellation', 13, WHITE),
    ('2. System calculates refund breakdown:', 13, WHITE),
    ('   room_refund + deposit_refund + other_refund', 12, TEAL),
    ('3. Refund record created (status = pending)', 13, YELLOW),
    ('4. Booking: status = 0, Transaction: cancelled', 13, RED),
    ('5. Room released immediately', 13, GREEN),
    ('', 6),
    ('Refund Confirmation (Admin):', 13, TEAL, True),
    ('6. Admin uploads proof of refund transfer', 13, WHITE),
    ('7. Refund status: pending → refunded', 13, GREEN),
    ('8. Transaction status: cancelled → refunded', 13, GREEN),
    ('', 6),
    ('Bank info required for QRIS/VA refunds', 12, LIGHT_GRAY),
])

# Room availability after cancellation
add_text_box(slide, 0.5, 4.7, 12, 0.5, 'Room Availability After Cancellation', 18, TEAL, True)
add_rounded_rect(slide, 0.5, 5.3, 12.3, 1.8, DARK_CARD)
add_multi_text_box(slide, 0.8, 5.4, 11.8, 1.6, [
    ('Daily Rooms: Booking dates removed from conflict list → Room available for those dates again', 14, BLUE),
    ('Monthly Rooms: rental_status = 0 → Room immediately visible in search results', 14, PURPLE),
    ('', 6),
    ('If cancelling a RENEWAL: Original booking is restored (renewal_status = 0, check_out_at = NULL)', 13, ORANGE),
    ('Parent booking continues as if renewal never happened. Room stays occupied by original tenant.', 13, LIGHT_GRAY),
])


# ============================================================
# SLIDE 10: Complete Flow Summary
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.2, 12, 0.6, 'Complete Flow Summary — Room Availability Timeline', 24, TEAL, True)

# Timeline header
steps_data = [
    ('Search', BLUE, 'Daily: Show all\nMonthly: status=0'),
    ('Booking\nCreated', TEAL, 'Daily: No change\nMonthly: status→1'),
    ('Payment\nConfirmed', GREEN, 'Daily: No change\nMonthly: Stays 1'),
    ('Check-In', PURPLE, 'Daily: No change\nMonthly: Stays 1'),
    ('Renewal', ORANGE, 'Daily: Extended\nMonthly: Stays 1'),
    ('Check-Out', PINK, 'Daily: Freed\nMonthly: status→0'),
    ('Cancelled', RED, 'Daily: Freed\nMonthly: status→0'),
]

for i, (label, color, desc) in enumerate(steps_data):
    x = 0.4 + i * 1.82
    add_rounded_rect(slide, x, 1.0, 1.6, 0.7, color, label, 11, WHITE, True)
    if i < len(steps_data) - 1:
        add_arrow(slide, x + 1.6, 1.2, 0.22, LIGHT_GRAY)
    # Description below
    add_text_box(slide, x, 1.8, 1.6, 0.8, desc, 9, LIGHT_GRAY, False, PP_ALIGN.CENTER)

# Daily timeline
add_text_box(slide, 0.5, 2.9, 2, 0.4, 'DAILY', 18, BLUE, True)
daily_statuses = [
    ('Available\n(no conflict)', GREEN),
    ('Available*\n(date blocked)', YELLOW),
    ('Available*\n(date blocked)', YELLOW),
    ('Available*\n(date blocked)', YELLOW),
    ('Available*\n(more dates)', YELLOW),
    ('Available\n(dates freed)', GREEN),
    ('Available\n(dates freed)', GREEN),
]
for i, (status, color) in enumerate(daily_statuses):
    x = 0.4 + i * 1.82
    add_rounded_rect(slide, x, 3.3, 1.6, 0.7, color, status, 9, DARK_BG, True)

add_text_box(slide, 0.5, 4.1, 12, 0.3, '* Room visible in search but blocked for conflicting dates only', 10, LIGHT_GRAY)

# Monthly timeline
add_text_box(slide, 0.5, 4.5, 2, 0.4, 'MONTHLY', 18, PURPLE, True)
monthly_statuses = [
    ('Available\nstatus = 0', GREEN),
    ('Blocked\nstatus = 1', RED),
    ('Blocked\nstatus = 1', RED),
    ('Occupied\nstatus = 1', RED),
    ('Occupied\nstatus = 1', RED),
    ('Available\nstatus = 0', GREEN),
    ('Available\nstatus = 0', GREEN),
]
for i, (status, color) in enumerate(monthly_statuses):
    x = 0.4 + i * 1.82
    add_rounded_rect(slide, x, 4.9, 1.6, 0.7, color, status, 9, WHITE if color == RED else DARK_BG, True)

# Key insights
add_text_box(slide, 0.5, 6.0, 12, 0.4, 'Key Insights', 16, TEAL, True)
add_multi_text_box(slide, 0.5, 6.4, 12, 1.0, [
    ('Daily rooms use date-conflict checking — room is always "available" but specific dates are blocked. Monthly rooms use a binary flag.', 12, WHITE),
    ('Monthly rooms are blocked immediately at booking (even before payment) to prevent double-booking during the 15-min payment window.', 12, WHITE),
    ('Renewals maintain continuous occupancy — room never becomes available between original and renewed booking.', 12, WHITE),
])

# Save
output_path = '/Users/trisnotjhin/Documents/GitHub/Ulin-Mahoni/Ulin_Mahoni_Booking_Flow.pptx'
prs.save(output_path)
print(f'Saved to {output_path}')
