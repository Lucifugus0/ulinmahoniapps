#!/usr/bin/env python3
"""Generate Ulin Mahoni Booking Flow PPTX presentation — Bahasa Indonesia."""

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


# ============================================================
# SLIDE 1: Judul
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 1, 1.5, 11, 1, 'ULIN MAHONI', 48, TEAL, True, PP_ALIGN.CENTER)
add_text_box(slide, 1, 2.5, 11, 1, 'Alur Pemesanan & Ketersediaan Kamar', 32, WHITE, False, PP_ALIGN.CENTER)
add_text_box(slide, 1, 3.5, 11, 0.5, 'Sewa Harian & Bulanan  |  Platform Kos Indonesia', 18, LIGHT_GRAY, False, PP_ALIGN.CENTER)

steps = ['Cari', 'Pesan', 'Bayar', 'Check-In', 'Perpanjang', 'Check-Out', 'Refund']
colors = [BLUE, TEAL, GREEN, PURPLE, ORANGE, PINK, RED]
x_start = 1.5
for i, (step, clr) in enumerate(zip(steps, colors)):
    add_rounded_rect(slide, x_start + i * 1.5, 5.2, 1.3, 0.5, clr, step, 12, WHITE, True)
    if i < len(steps) - 1:
        add_arrow(slide, x_start + i * 1.5 + 1.3, 5.3, 0.2, LIGHT_GRAY)

add_text_box(slide, 1, 6.5, 11, 0.4, 'Siklus lengkap dari pencarian kamar hingga pengembalian deposit', 14, LIGHT_GRAY, False, PP_ALIGN.CENTER)


# ============================================================
# SLIDE 2: Gambaran Umum — Harian vs Bulanan
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 12, 0.6, 'Gambaran Umum Sewa Harian vs Bulanan', 28, TEAL, True)

# Kolom Harian
add_rounded_rect(slide, 0.5, 1.2, 5.8, 5.5, DARK_CARD)
add_text_box(slide, 0.8, 1.3, 5, 0.5, 'SEWA HARIAN', 22, BLUE, True)
add_multi_text_box(slide, 0.8, 1.9, 5.2, 4.5, [
    ('Batasan Pemesanan', 14, TEAL, True),
    ('  Maks tanggal check-in: 90 hari dari hari ini', 13, WHITE),
    ('  Maks durasi menginap: 60 hari', 13, WHITE),
    ('  Jam check-in: 14.00  |  Jam check-out: 12.00', 13, WHITE),
    ('', 8),
    ('Ketersediaan Kamar', 14, TEAL, True),
    ('  Kamar SELALU tampil di hasil pencarian', 13, WHITE),
    ('  Flag rental_status DIABAIKAN untuk kamar harian', 13, WHITE),
    ('  Ketersediaan dicek berdasarkan konflik tanggal', 13, WHITE),
    ('  Pergantian tamu di hari yang sama diizinkan (12.00 keluar, 14.00 masuk)', 13, WHITE),
    ('', 8),
    ('Harga', 14, TEAL, True),
    ('  5 tingkat harga per tanggal:', 13, WHITE),
    ('  High Season > Low Season > Hari Libur > Akhir Pekan > Hari Kerja', 13, YELLOW),
    ('  Harga per tanggal disimpan di m_room_prices', 13, WHITE),
    ('  Override harga manual tetap dipertahankan', 13, WHITE),
])

# Kolom Bulanan
add_rounded_rect(slide, 6.8, 1.2, 5.8, 5.5, DARK_CARD)
add_text_box(slide, 7.1, 1.3, 5, 0.5, 'SEWA BULANAN', 22, PURPLE, True)
add_multi_text_box(slide, 7.1, 1.9, 5.2, 4.5, [
    ('Batasan Pemesanan', 14, TEAL, True),
    ('  Maks tanggal check-in: 14 hari dari hari ini', 13, WHITE),
    ('  Maks durasi menginap: 12 bulan', 13, WHITE),
    ('  Tanggal check-in: fleksibel  |  Check-out: tanggal sama bulan berikutnya', 13, WHITE),
    ('', 8),
    ('Ketersediaan Kamar', 14, TEAL, True),
    ('  Dikendalikan oleh flag rental_status', 13, WHITE),
    ('  rental_status = 0 : Tersedia (tampil di pencarian)', 13, GREEN),
    ('  rental_status = 1 : Terisi (tersembunyi dari pencarian)', 13, RED),
    ('  Status langsung diblokir saat pemesanan dibuat', 13, WHITE),
    ('', 8),
    ('Harga', 14, TEAL, True),
    ('  Harga bulanan tetap dari data kamar', 13, WHITE),
    ('  Harga dikunci saat pemesanan', 13, WHITE),
    ('  Perpanjangan menggunakan harga terbaru (bisa berbeda)', 13, YELLOW),
    ('  original_checkin_day dipertahankan antar perpanjangan', 13, WHITE),
])


# ============================================================
# SLIDE 3: Langkah 1 — Cari Kamar
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 3, 0.6, 'Langkah 1', 16, LIGHT_GRAY, False)
add_text_box(slide, 2.0, 0.3, 10, 0.6, 'Cari Kamar', 28, BLUE, True)

# Aksi pengguna
add_rounded_rect(slide, 0.5, 1.2, 6, 2.5, DARK_CARD)
add_text_box(slide, 0.8, 1.3, 5, 0.4, 'AKSI PENGGUNA', 16, TEAL, True)
add_multi_text_box(slide, 0.8, 1.8, 5.5, 2.0, [
    ('1. Pilih properti (atau lihat semua)', 13, WHITE),
    ('2. Pilih periode sewa: Harian atau Bulanan', 13, WHITE),
    ('3. Pilih tanggal check-in dan check-out', 13, WHITE),
    ('4. Sistem memfilter kamar yang tersedia', 13, WHITE),
    ('5. Lihat detail kamar, foto, fasilitas, harga', 13, WHITE),
])

# Logika sistem
add_rounded_rect(slide, 7, 1.2, 5.8, 2.5, DARK_CARD)
add_text_box(slide, 7.3, 1.3, 5, 0.4, 'LOGIKA SISTEM', 16, TEAL, True)
add_multi_text_box(slide, 7.3, 1.8, 5.2, 2.0, [
    ('Harian: Tampilkan semua kamar aktif (status=1)', 13, BLUE),
    ('  → Kecualikan kamar dengan konflik tanggal', 12, LIGHT_GRAY),
    ('  → Batas check-in 14.00 / check-out 12.00', 12, LIGHT_GRAY),
    ('Bulanan: Filter berdasarkan rental_status = 0', 13, PURPLE),
    ('  → Hanya tampilkan kamar yang tidak terisi', 12, LIGHT_GRAY),
    ('  → Langsung diblokir saat pemesanan dibuat', 12, LIGHT_GRAY),
])

# Ketersediaan kamar
add_text_box(slide, 0.5, 4.0, 12, 0.5, 'Ketersediaan Kamar pada Langkah Ini', 18, TEAL, True)

# Contoh harian
add_rounded_rect(slide, 0.5, 4.6, 6, 2.5, DARK_CARD)
add_text_box(slide, 0.8, 4.7, 5, 0.4, 'KAMAR HARIAN', 14, BLUE, True)
rooms_daily = [
    ('Kamar 101', 'available', 'Apr 10-12 kosong'),
    ('Kamar 102', 'available', 'Apr 10-12 kosong'),
    ('Kamar 103', 'occupied', 'Apr 10-11 dipesan tamu lain'),
    ('Kamar 104', 'available', 'Apr 10-12 kosong'),
]
for i, (name, status, note) in enumerate(rooms_daily):
    y = 5.2 + i * 0.4
    add_text_box(slide, 0.8, y, 1.2, 0.3, name, 11, WHITE, True)
    clr = GREEN if status == 'available' else RED
    lbl = 'TERSEDIA' if status == 'available' else 'KONFLIK'
    add_rounded_rect(slide, 2.2, y, 1.1, 0.3, clr, lbl, 9, WHITE, True)
    add_text_box(slide, 3.5, y, 3, 0.3, note, 10, LIGHT_GRAY)

# Contoh bulanan
add_rounded_rect(slide, 7, 4.6, 5.8, 2.5, DARK_CARD)
add_text_box(slide, 7.3, 4.7, 5, 0.4, 'KAMAR BULANAN', 14, PURPLE, True)
rooms_monthly = [
    ('Kamar 201', 'available', 'rental_status = 0'),
    ('Kamar 202', 'occupied', 'rental_status = 1 (terisi)'),
    ('Kamar 203', 'available', 'rental_status = 0'),
    ('Kamar 204', 'occupied', 'rental_status = 1 (terisi)'),
]
for i, (name, status, note) in enumerate(rooms_monthly):
    y = 5.2 + i * 0.4
    add_text_box(slide, 7.3, y, 1.2, 0.3, name, 11, WHITE, True)
    clr = GREEN if status == 'available' else RED
    lbl = 'TERSEDIA' if status == 'available' else 'TERISI'
    add_rounded_rect(slide, 8.7, y, 1.1, 0.3, clr, lbl, 9, WHITE, True)
    add_text_box(slide, 10.0, y, 2.5, 0.3, note, 10, LIGHT_GRAY)


# ============================================================
# SLIDE 4: Langkah 2 — Buat Pemesanan
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 3, 0.6, 'Langkah 2', 16, LIGHT_GRAY, False)
add_text_box(slide, 2.0, 0.3, 10, 0.6, 'Buat Pemesanan', 28, TEAL, True)

# Alur
add_rounded_rect(slide, 0.5, 1.2, 3.8, 1.8, DARK_CARD)
add_multi_text_box(slide, 0.7, 1.3, 3.4, 1.6, [
    ('Pengguna Kirim Pemesanan', 14, TEAL, True),
    ('POST /api/v1/booking', 11, LIGHT_GRAY),
    ('', 6),
    ('Pilih kamar, tanggal, parkir', 12, WHITE),
    ('Tinjau rincian harga', 12, WHITE),
    ('Konfirmasi permintaan pesanan', 12, WHITE),
])

add_arrow(slide, 4.3, 1.9, 0.4, TEAL)

add_rounded_rect(slide, 4.9, 1.2, 3.8, 1.8, DARK_CARD)
add_multi_text_box(slide, 5.1, 1.3, 3.4, 1.6, [
    ('Sistem Membuat Data', 14, TEAL, True),
    ('', 6),
    ('Transaksi: status = pending', 12, YELLOW),
    ('Pemesanan: status = 1 (aktif)', 12, GREEN),
    ('Pembayaran: status = belum bayar', 12, RED),
    ('Kedaluwarsa: hitung mundur 15 menit', 12, ORANGE, True),
])

add_arrow(slide, 8.7, 1.9, 0.4, TEAL)

add_rounded_rect(slide, 9.3, 1.2, 3.5, 1.8, DARK_CARD)
add_multi_text_box(slide, 9.5, 1.3, 3.1, 1.6, [
    ('Jika Kedaluwarsa (15 menit)', 14, RED, True),
    ('', 6),
    ('Transaksi: status = expired', 12, RED),
    ('Pemesanan: status = 0 (nonaktif)', 12, RED),
    ('Bulanan: rental_status = 0', 12, GREEN),
    ('Kamar dilepas kembali ke pool', 12, GREEN),
])

# Dampak ketersediaan kamar
add_text_box(slide, 0.5, 3.3, 12, 0.5, 'Dampak Ketersediaan Kamar', 18, TEAL, True)

add_rounded_rect(slide, 0.5, 3.9, 6, 3.0, DARK_CARD)
add_text_box(slide, 0.8, 4.0, 5, 0.4, 'KAMAR HARIAN DIPESAN', 14, BLUE, True)
add_multi_text_box(slide, 0.8, 4.5, 5.5, 2.2, [
    ('Kamar 101 dipesan untuk 10-12 Apr', 13, WHITE),
    ('', 6),
    ('rental_status: TIDAK BERUBAH (tetap 0)', 13, GREEN, True),
    ('', 4),
    ('Pengguna lain mencari 10-12 Apr:', 12, LIGHT_GRAY),
    ('  Kamar 101: tersembunyi (konflik tanggal)', 12, RED),
    ('  Kamar 102: masih tersedia', 12, GREEN),
    ('', 4),
    ('Pengguna lain mencari 13-15 Apr:', 12, LIGHT_GRAY),
    ('  Kamar 101: tersedia (tidak ada konflik)', 12, GREEN),
])

add_rounded_rect(slide, 7, 3.9, 5.8, 3.0, DARK_CARD)
add_text_box(slide, 7.3, 4.0, 5, 0.4, 'KAMAR BULANAN DIPESAN', 14, PURPLE, True)
add_multi_text_box(slide, 7.3, 4.5, 5.2, 2.2, [
    ('Kamar 201 dipesan untuk Apr-Mei', 13, WHITE),
    ('', 6),
    ('rental_status: LANGSUNG DISET KE 1', 13, RED, True),
    ('', 4),
    ('Pengguna lain mencari tanggal apapun:', 12, LIGHT_GRAY),
    ('  Kamar 201: tersembunyi dari semua pencarian', 12, RED),
    ('  Kamar 203: masih tersedia (status=0)', 12, GREEN),
    ('', 4),
    ('Kamar diblokir bahkan selama jendela', 12, YELLOW),
    ('pembayaran 15 menit untuk cegah double-booking', 12, YELLOW),
])


# ============================================================
# SLIDE 5: Langkah 3 — Pembayaran
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 3, 0.6, 'Langkah 3', 16, LIGHT_GRAY, False)
add_text_box(slide, 2.0, 0.3, 10, 0.6, 'Pembayaran', 28, GREEN, True)

# Metode pembayaran
add_rounded_rect(slide, 0.5, 1.2, 4.0, 3.5, DARK_CARD)
add_text_box(slide, 0.8, 1.3, 3.5, 0.4, 'METODE PEMBAYARAN', 16, TEAL, True)
add_multi_text_box(slide, 0.8, 1.8, 3.5, 2.8, [
    ('Virtual Account (VA)', 14, BLUE, True),
    ('  BRI, BNI, BCA, Mandiri, CIMB', 12, WHITE),
    ('  Nomor VA unik per pemesanan', 11, LIGHT_GRAY),
    ('', 6),
    ('QRIS', 14, GREEN, True),
    ('  Pindai kode QR untuk membayar', 12, WHITE),
    ('  Didukung semua bank Indonesia', 11, LIGHT_GRAY),
    ('', 6),
    ('Kartu Kredit', 14, PURPLE, True),
    ('  Pembayaran kartu langsung via DOKU', 12, WHITE),
    ('', 6),
    ('Didukung oleh DOKU Payment Gateway', 11, YELLOW),
])

# Alur pembayaran
add_rounded_rect(slide, 4.8, 1.2, 4.0, 3.5, DARK_CARD)
add_text_box(slide, 5.1, 1.3, 3.5, 0.4, 'ALUR PEMBAYARAN', 16, TEAL, True)
add_multi_text_box(slide, 5.1, 1.8, 3.5, 2.8, [
    ('1. Pengguna pilih metode pembayaran', 13, WHITE),
    ('2. Sistem buat kode bayar/QR', 13, WHITE),
    ('3. Pengguna selesaikan pembayaran', 13, WHITE),
    ('4. DOKU kirim webhook callback', 13, WHITE),
    ('5. Sistem verifikasi & update status', 13, WHITE),
    ('', 8),
    ('Transisi Status:', 13, TEAL, True),
    ('  pending  →  paid (lunas)', 13, GREEN, True),
    ('', 6),
    ('Batas pembayaran: 15 menit', 12, ORANGE),
    ('Otomatis kedaluwarsa jika tidak dibayar', 12, ORANGE),
    ('Admin bisa verifikasi manual', 12, LIGHT_GRAY),
])

# Setelah pembayaran
add_rounded_rect(slide, 9.1, 1.2, 3.7, 3.5, DARK_CARD)
add_text_box(slide, 9.4, 1.3, 3.2, 0.4, 'SETELAH PEMBAYARAN', 16, TEAL, True)
add_multi_text_box(slide, 9.4, 1.8, 3.2, 2.8, [
    ('Transaksi:', 13, WHITE, True),
    ('  status = paid (lunas)', 13, GREEN, True),
    ('', 6),
    ('Pemesanan:', 13, WHITE, True),
    ('  status = 1 (aktif)', 13, GREEN),
    ('  Menunggu check-in', 13, YELLOW),
    ('', 6),
    ('Notifikasi Push:', 13, WHITE, True),
    ('  Dikirim ke tamu + admin', 13, TEAL),
    ('', 6),
    ('Ketersediaan Kamar:', 13, WHITE, True),
    ('  Harian: tidak berubah', 13, BLUE),
    ('  Bulanan: tetap terblokir', 13, PURPLE),
])

# Ketersediaan kamar
add_text_box(slide, 0.5, 5.0, 12, 0.5, 'Ketersediaan Kamar: Tidak Ada Perubahan dari Langkah 2', 16, TEAL, True)
add_multi_text_box(slide, 0.5, 5.5, 12, 1.5, [
    ('Kamar harian: Tetap tersedia untuk tanggal yang tidak konflik. Filter berbasis konflik berlanjut.', 13, BLUE),
    ('Kamar bulanan: rental_status tetap 1. Kamar tetap tersembunyi dari semua pencarian.', 13, PURPLE),
    ('Konfirmasi pembayaran tidak mengubah ketersediaan kamar — sudah direservasi saat pembuatan pesanan.', 13, LIGHT_GRAY),
])


# ============================================================
# SLIDE 6: Langkah 4 — Check-In
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 3, 0.6, 'Langkah 4', 16, LIGHT_GRAY, False)
add_text_box(slide, 2.0, 0.3, 10, 0.6, 'Check-In', 28, PURPLE, True)

# Proses admin
add_rounded_rect(slide, 0.5, 1.2, 6, 3.0, DARK_CARD)
add_text_box(slide, 0.8, 1.3, 5, 0.4, 'PROSES CHECK-IN ADMIN', 16, TEAL, True)
add_multi_text_box(slide, 0.8, 1.8, 5.5, 2.3, [
    ('1. Admin buka halaman Pemesanan Terkonfirmasi', 13, WHITE),
    ('2. Klik tombol Check-In pada pemesanan', 13, WHITE),
    ('3. Unggah dokumen identitas tamu:', 13, WHITE),
    ('   KTP / Paspor / SIM / Lainnya', 12, LIGHT_GRAY),
    ('4. Verifikasi identitas tamu (NIK, foto)', 13, WHITE),
    ('5. Sistem mencatat:', 13, WHITE),
    ('   check_in_at = waktu saat ini', 12, TEAL),
    ('   checked_in_by = ID admin', 12, TEAL),
    ('6. Cetak formulir registrasi & invoice', 13, WHITE),
])

# Perubahan status
add_rounded_rect(slide, 7, 1.2, 5.8, 3.0, DARK_CARD)
add_text_box(slide, 7.3, 1.3, 5, 0.4, 'PERUBAHAN STATUS', 16, TEAL, True)
add_multi_text_box(slide, 7.3, 1.8, 5.2, 2.3, [
    ('Sebelum Check-In:', 14, YELLOW, True),
    ('  Transaksi: paid (lunas)', 12, GREEN),
    ('  Pemesanan: status = 1, check_in_at = NULL', 12, WHITE),
    ('  Kondisi: "Menunggu Check-In"', 12, YELLOW),
    ('', 8),
    ('Setelah Check-In:', 14, GREEN, True),
    ('  Transaksi: paid (tidak berubah)', 12, GREEN),
    ('  Pemesanan: check_in_at = timestamp', 12, TEAL),
    ('  Kondisi: "Sudah Check-In"', 12, GREEN),
    ('  check_out_at = NULL (menunggu checkout)', 12, WHITE),
])

# Ketersediaan kamar
add_text_box(slide, 0.5, 4.5, 12, 0.5, 'Ketersediaan Kamar pada Langkah Ini', 18, TEAL, True)
add_rounded_rect(slide, 0.5, 5.1, 12.3, 2.0, DARK_CARD)
add_multi_text_box(slide, 0.8, 5.2, 11.8, 1.8, [
    ('Kamar Harian: TIDAK BERUBAH — ketersediaan tetap ditentukan oleh konflik tanggal saja', 14, BLUE),
    ('Kamar Bulanan: TIDAK BERUBAH — rental_status tetap 1 (sudah diset saat pembuatan pesanan)', 14, PURPLE),
    ('', 8),
    ('Check-in fisik tidak mengubah flag ketersediaan apapun.', 13, WHITE),
    ('Kamar sudah direservasi/diblokir sejak Langkah 2 (pembuatan pesanan).', 13, LIGHT_GRAY),
    ('Tamu sekarang secara fisik menempati kamar.', 13, LIGHT_GRAY),
])


# ============================================================
# SLIDE 7: Langkah 5 — Perpanjangan
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 3, 0.6, 'Langkah 5', 16, LIGHT_GRAY, False)
add_text_box(slide, 2.0, 0.3, 10, 0.6, 'Perpanjangan (Extend Menginap)', 28, ORANGE, True)

# Proses perpanjangan
add_rounded_rect(slide, 0.5, 1.1, 6, 3.2, DARK_CARD)
add_text_box(slide, 0.8, 1.2, 5, 0.4, 'PROSES PERPANJANGAN', 16, TEAL, True)
add_multi_text_box(slide, 0.8, 1.7, 5.5, 2.8, [
    ('Pemicu: Tamu ingin memperpanjang masa tinggal', 13, WHITE),
    ('POST /api/v1/booking/{order_id}/renew', 11, LIGHT_GRAY),
    ('', 6),
    ('1. Validasi pemesanan asli sudah lunas', 13, WHITE),
    ('2. Lewati cek rental_status (penyewa sama)', 13, YELLOW),
    ('3. Buat transaksi BARU (is_renewal = 1)', 13, WHITE),
    ('4. Buat data pemesanan BARU', 13, WHITE),
    ('5. Data pembayaran baru (belum bayar, 15 menit)', 13, WHITE),
    ('', 6),
    ('Update Pemesanan Asli:', 13, TEAL, True),
    ('  check_out_at = sekarang (dicatat untuk arsip)', 12, WHITE),
    ('  renewal_status = 1 (ditandai sudah diperpanjang)', 12, YELLOW),
    ('  Kuota parkir TIDAK ditambah', 12, ORANGE),
])

# Kontinuitas
add_rounded_rect(slide, 7, 1.1, 5.8, 3.2, DARK_CARD)
add_text_box(slide, 7.3, 1.2, 5, 0.4, 'OKUPANSI BERKELANJUTAN', 16, TEAL, True)
add_multi_text_box(slide, 7.3, 1.7, 5.2, 2.8, [
    ('Prinsip Utama:', 14, ORANGE, True),
    ('Kamar TIDAK PERNAH menjadi tersedia antara', 13, WHITE),
    ('pemesanan asli dan perpanjangan', 13, WHITE),
    ('', 6),
    ('Rantai Perpanjangan Bulanan:', 13, PURPLE, True),
    ('  Check-in 31 Jan → Perpanjang ke Feb', 12, WHITE),
    ('  original_checkin_day = 31 dipertahankan', 12, YELLOW),
    ('  Perpanjangan berikut: 31 Mar (bukan 28 Feb)', 12, WHITE),
    ('  Mencegah kehilangan hari antar bulan', 12, LIGHT_GRAY),
    ('', 6),
    ('Perpanjangan Harian:', 13, BLUE, True),
    ('  Cukup memperpanjang rentang tanggal', 12, WHITE),
    ('  Cek konflik baru untuk tanggal perpanjangan', 12, WHITE),
    ('  Pemesanan tamu lain tetap dihormati', 12, LIGHT_GRAY),
])

# Ketersediaan kamar
add_text_box(slide, 0.5, 4.5, 12, 0.5, 'Ketersediaan Kamar pada Langkah Ini', 18, TEAL, True)
add_rounded_rect(slide, 0.5, 5.1, 12.3, 2.0, DARK_CARD)
add_multi_text_box(slide, 0.8, 5.2, 11.8, 1.8, [
    ('Kamar Harian: Rentang tanggal diperpanjang masuk ke cek konflik. Kamar diblokir untuk tanggal asli + perpanjangan.', 14, BLUE),
    ('Kamar Bulanan: rental_status TETAP 1 — okupansi berkelanjutan, tanpa jeda. Kamar tetap tersembunyi dari pencarian.', 14, PURPLE),
    ('', 8),
    ('Jika pembayaran perpanjangan kedaluwarsa (15 menit): Kamar kembali ke status pemesanan asli.', 13, ORANGE),
    ('Jika perpanjangan dibatalkan: Pemesanan asli dipulihkan (renewal_status = 0, check_out_at = NULL).', 13, LIGHT_GRAY),
])


# ============================================================
# SLIDE 8: Langkah 6 — Check-Out
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 3, 0.6, 'Langkah 6', 16, LIGHT_GRAY, False)
add_text_box(slide, 2.0, 0.3, 10, 0.6, 'Check-Out', 28, PINK, True)

# Proses admin
add_rounded_rect(slide, 0.5, 1.2, 6, 3.0, DARK_CARD)
add_text_box(slide, 0.8, 1.3, 5, 0.4, 'PROSES CHECK-OUT ADMIN', 16, TEAL, True)
add_multi_text_box(slide, 0.8, 1.8, 5.5, 2.3, [
    ('1. Admin buka halaman "Check-out Hari Ini"', 13, WHITE),
    ('   Menampilkan pemesanan jatuh tempo atau terlambat', 12, LIGHT_GRAY),
    ('2. Buka modal checkout untuk pemesanan', 13, WHITE),
    ('3. Catat kondisi item kamar', 13, WHITE),
    ('   Catat kerusakan & biaya tambahan', 12, LIGHT_GRAY),
    ('4. Tambah catatan checkout jika perlu', 13, WHITE),
    ('5. Konfirmasi check-out', 13, WHITE),
    ('6. Sistem mencatat:', 13, WHITE),
    ('   check_out_at = waktu saat ini', 12, TEAL),
    ('   checked_out_by = ID admin', 12, TEAL),
])

# Perubahan status
add_rounded_rect(slide, 7, 1.2, 5.8, 3.0, DARK_CARD)
add_text_box(slide, 7.3, 1.3, 5, 0.4, 'PERUBAHAN STATUS', 16, TEAL, True)
add_multi_text_box(slide, 7.3, 1.8, 5.2, 2.3, [
    ('Pemesanan:', 14, WHITE, True),
    ('  status = 0 (nonaktif)', 13, RED),
    ('  check_out_at = timestamp', 13, WHITE),
    ('', 6),
    ('Transaksi:', 14, WHITE, True),
    ('  status = paid (tidak berubah)', 13, GREEN),
    ('', 6),
    ('Parkir:', 14, WHITE, True),
    ('  Kuota dikurangi (dibebaskan)', 13, TEAL),
    ('  Dicek dari 3 sumber', 13, LIGHT_GRAY),
])

# Ketersediaan kamar - PERUBAHAN BESAR
add_text_box(slide, 0.5, 4.5, 12, 0.5, 'Ketersediaan Kamar pada Langkah Ini', 18, TEAL, True)
add_rounded_rect(slide, 0.5, 5.1, 6, 2.0, DARK_CARD)
add_text_box(slide, 0.8, 5.2, 5, 0.4, 'JIKA TIDAK ADA PEMESANAN AKTIF LAIN', 13, GREEN, True)
add_multi_text_box(slide, 0.8, 5.7, 5.5, 1.3, [
    ('Harian: Tanggal kamar dibebaskan dari daftar konflik', 13, BLUE),
    ('Bulanan: rental_status = 0', 13, GREEN, True),
    ('Kamar kembali ke pool tersedia', 13, GREEN),
    ('Terlihat di hasil pencarian lagi', 13, WHITE),
])

add_rounded_rect(slide, 7, 5.1, 5.8, 2.0, DARK_CARD)
add_text_box(slide, 7.3, 5.2, 5, 0.4, 'JIKA ADA PEMESANAN AKTIF LAIN', 13, YELLOW, True)
add_multi_text_box(slide, 7.3, 5.7, 5.2, 1.3, [
    ('Tamu berikutnya sudah punya pemesanan kamar ini', 13, WHITE),
    ('Bulanan: rental_status tetap 1', 13, RED),
    ('Kamar tetap terblokir untuk penghuni berikutnya', 13, YELLOW),
    ('Transisi mulus antar penyewa', 13, LIGHT_GRAY),
])


# ============================================================
# SLIDE 9: Langkah 7 — Refund Deposit
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.3, 3, 0.6, 'Langkah 7', 16, LIGHT_GRAY, False)
add_text_box(slide, 2.0, 0.3, 10, 0.6, 'Refund & Deposit', 28, RED, True)

# Tingkat refund pembatalan
add_rounded_rect(slide, 0.5, 1.1, 5.5, 3.4, DARK_CARD)
add_text_box(slide, 0.8, 1.2, 5, 0.4, 'TINGKAT REFUND PEMBATALAN', 16, TEAL, True)
add_multi_text_box(slide, 0.8, 1.7, 5.0, 2.8, [
    ('Hari Sebelum Check-In → % Refund', 13, WHITE, True),
    ('', 6),
    ('  > 10 hari :  75% kamar + parkir', 14, GREEN),
    ('  9-7 hari  :  50% kamar + parkir', 14, YELLOW),
    ('  6-3 hari  :  25% kamar + parkir', 14, ORANGE),
    ('  < 3 hari  :   0% (tidak ada refund)', 14, RED),
    ('', 8),
    ('Deposit: SELALU dikembalikan 100%', 14, GREEN, True),
    ('Biaya layanan/admin: TIDAK dikembalikan', 13, RED),
    ('Admin bisa override jumlah kalkulasi', 12, LIGHT_GRAY),
])

# Proses refund
add_rounded_rect(slide, 6.3, 1.1, 6.5, 3.4, DARK_CARD)
add_text_box(slide, 6.6, 1.2, 6, 0.4, 'PROSES REFUND', 16, TEAL, True)
add_multi_text_box(slide, 6.6, 1.7, 6.0, 2.8, [
    ('1. Pengguna/Admin memulai pembatalan', 13, WHITE),
    ('2. Sistem menghitung rincian refund:', 13, WHITE),
    ('   refund_kamar + refund_deposit + refund_lainnya', 12, TEAL),
    ('3. Data refund dibuat (status = pending)', 13, YELLOW),
    ('4. Pemesanan: status = 0, Transaksi: dibatalkan', 13, RED),
    ('5. Kamar langsung dilepas', 13, GREEN),
    ('', 6),
    ('Konfirmasi Refund (Admin):', 13, TEAL, True),
    ('6. Admin unggah bukti transfer refund', 13, WHITE),
    ('7. Status refund: pending → refunded', 13, GREEN),
    ('8. Status transaksi: dibatalkan → refunded', 13, GREEN),
    ('', 6),
    ('Info bank diperlukan untuk refund QRIS/VA', 12, LIGHT_GRAY),
])

# Ketersediaan kamar setelah pembatalan
add_text_box(slide, 0.5, 4.7, 12, 0.5, 'Ketersediaan Kamar Setelah Pembatalan', 18, TEAL, True)
add_rounded_rect(slide, 0.5, 5.3, 12.3, 1.8, DARK_CARD)
add_multi_text_box(slide, 0.8, 5.4, 11.8, 1.6, [
    ('Kamar Harian: Tanggal pemesanan dihapus dari daftar konflik → Kamar tersedia untuk tanggal tersebut lagi', 14, BLUE),
    ('Kamar Bulanan: rental_status = 0 → Kamar langsung terlihat di hasil pencarian', 14, PURPLE),
    ('', 6),
    ('Jika membatalkan PERPANJANGAN: Pemesanan asli dipulihkan (renewal_status = 0, check_out_at = NULL)', 13, ORANGE),
    ('Pemesanan induk berlanjut seolah perpanjangan tidak pernah terjadi. Kamar tetap ditempati penyewa asli.', 13, LIGHT_GRAY),
])


# ============================================================
# SLIDE 10: Ringkasan Alur Lengkap
# ============================================================
slide = prs.slides.add_slide(prs.slide_layouts[6])
set_slide_bg(slide, DARK_BG)

add_text_box(slide, 0.5, 0.2, 12, 0.6, 'Ringkasan Alur Lengkap — Timeline Ketersediaan Kamar', 24, TEAL, True)

# Header timeline
steps_data = [
    ('Cari', BLUE, 'Harian: Tampil semua\nBulanan: status=0'),
    ('Pesanan\nDibuat', TEAL, 'Harian: Tidak berubah\nBulanan: status→1'),
    ('Bayar\nLunas', GREEN, 'Harian: Tidak berubah\nBulanan: Tetap 1'),
    ('Check-In', PURPLE, 'Harian: Tidak berubah\nBulanan: Tetap 1'),
    ('Perpanjang', ORANGE, 'Harian: Diperluas\nBulanan: Tetap 1'),
    ('Check-Out', PINK, 'Harian: Dibebaskan\nBulanan: status→0'),
    ('Dibatalkan', RED, 'Harian: Dibebaskan\nBulanan: status→0'),
]

for i, (label, color, desc) in enumerate(steps_data):
    x = 0.4 + i * 1.82
    add_rounded_rect(slide, x, 1.0, 1.6, 0.7, color, label, 11, WHITE, True)
    if i < len(steps_data) - 1:
        add_arrow(slide, x + 1.6, 1.2, 0.22, LIGHT_GRAY)
    add_text_box(slide, x, 1.8, 1.6, 0.8, desc, 9, LIGHT_GRAY, False, PP_ALIGN.CENTER)

# Timeline harian
add_text_box(slide, 0.5, 2.9, 2, 0.4, 'HARIAN', 18, BLUE, True)
daily_statuses = [
    ('Tersedia\n(tanpa konflik)', GREEN),
    ('Tersedia*\n(tgl diblokir)', YELLOW),
    ('Tersedia*\n(tgl diblokir)', YELLOW),
    ('Tersedia*\n(tgl diblokir)', YELLOW),
    ('Tersedia*\n(lebih banyak tgl)', YELLOW),
    ('Tersedia\n(tgl dibebaskan)', GREEN),
    ('Tersedia\n(tgl dibebaskan)', GREEN),
]
for i, (status, color) in enumerate(daily_statuses):
    x = 0.4 + i * 1.82
    add_rounded_rect(slide, x, 3.3, 1.6, 0.7, color, status, 9, DARK_BG, True)

add_text_box(slide, 0.5, 4.1, 12, 0.3, '* Kamar tampil di pencarian tapi diblokir untuk tanggal yang konflik saja', 10, LIGHT_GRAY)

# Timeline bulanan
add_text_box(slide, 0.5, 4.5, 2, 0.4, 'BULANAN', 18, PURPLE, True)
monthly_statuses = [
    ('Tersedia\nstatus = 0', GREEN),
    ('Terblokir\nstatus = 1', RED),
    ('Terblokir\nstatus = 1', RED),
    ('Ditempati\nstatus = 1', RED),
    ('Ditempati\nstatus = 1', RED),
    ('Tersedia\nstatus = 0', GREEN),
    ('Tersedia\nstatus = 0', GREEN),
]
for i, (status, color) in enumerate(monthly_statuses):
    x = 0.4 + i * 1.82
    add_rounded_rect(slide, x, 4.9, 1.6, 0.7, color, status, 9, WHITE if color == RED else DARK_BG, True)

# Insight utama
add_text_box(slide, 0.5, 6.0, 12, 0.4, 'Insight Utama', 16, TEAL, True)
add_multi_text_box(slide, 0.5, 6.4, 12, 1.0, [
    ('Kamar harian menggunakan pengecekan konflik tanggal — kamar selalu "tersedia" tapi tanggal tertentu diblokir. Kamar bulanan menggunakan flag biner.', 12, WHITE),
    ('Kamar bulanan langsung diblokir saat pemesanan (bahkan sebelum bayar) untuk mencegah double-booking selama jendela pembayaran 15 menit.', 12, WHITE),
    ('Perpanjangan menjaga okupansi berkelanjutan — kamar tidak pernah menjadi tersedia antara pemesanan asli dan perpanjangan.', 12, WHITE),
])

# Simpan
output_path = '/Users/trisnotjhin/Documents/GitHub/Ulin-Mahoni/Ulin_Mahoni_Alur_Pemesanan.pptx'
prs.save(output_path)
print(f'Tersimpan di {output_path}')
