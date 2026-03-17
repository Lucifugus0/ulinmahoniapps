# 🚗 Sistem Kwota Parkir (Parking Quota System)

## 📋 Deskripsi
Sistem kwota parkir memungkinkan setiap property untuk mengelola kapasitas parkir dengan tracking real-time untuk mobil dan motor secara terpisah. Sistem ini otomatis memotong kwota saat ada pembayaran parkir dan mengembalikan kwota saat kendaraan check-out.

---

## 🗄️ Struktur Database

### Tabel: `m_parking_fee`
Kolom baru yang ditambahkan:

| Kolom | Tipe | Default | Deskripsi |
|-------|------|---------|-----------|
| `quota_used` | INTEGER | 0 | Jumlah kwota yang sedang digunakan |

### Kolom yang sudah ada:
- `capacity`: Kwota maksimal (kapasitas total)
  - **`capacity = 0`** → **UNLIMITED** (tidak ada batasan parkir)
  - **`capacity > 0`** → **LIMITED** (ada batasan parkir dengan quota system)
- `parking_type`: Tipe parkir (`car` / `motorcycle`)
- `property_id`: ID property

### Constraint:
- Unique constraint: `['property_id', 'parking_type']`
- Artinya: Setiap property memiliki kwota terpisah untuk mobil dan motor

---

## 💡 Konsep Penting: Capacity = 0 (Unlimited)

### Apa artinya Capacity = 0?
- **Tidak ada batasan parkir**
- Bisa menerima parking payment tanpa batas
- `quota_used` tidak akan di-increment
- Cocok untuk property yang tidak ingin membatasi parkir

### Kapan Parking Fee Auto-Created?
Ketika ada parking payment dibuat untuk property yang **belum memiliki parking fee**:
1. Sistem otomatis membuat parking fee baru
2. Default `capacity = 0` (unlimited)
3. User bisa update capacity di **Parking Fee Management** nanti

### Cara Mengaktifkan Quota System
1. Buka menu **Parking Fee Management**
2. Edit parking fee untuk property
3. Set `capacity` > 0 (contoh: 50 untuk 50 mobil)
4. Save → Quota system aktif!

---

## 🔄 Alur Kerja Sistem

### 1️⃣ Saat Membuat Parking Payment (Create)
```
1. User membuat parking payment baru
2. Sistem cek apakah parking fee sudah ada:
   - Jika BELUM ada → Auto-create dengan capacity = 0 (unlimited)
   - Jika SUDAH ada → Lanjut ke step 3
3. Sistem cek ketersediaan kwota:
   - Jika capacity = 0 → UNLIMITED (skip quota check)
   - Jika capacity > 0:
     * available_quota = capacity - quota_used
     * Jika available_quota > 0 → Lanjut
     * Jika available_quota <= 0 → Error: "Parking quota is full"
4. Jika kwota tersedia atau unlimited:
   - Transaksi dibuat dengan status 'paid'
   - quota_used bertambah +1 (hanya jika capacity > 0)
   - available_quota berkurang -1 (hanya jika capacity > 0)
```

### 2️⃣ Saat Kendaraan Check-Out (Checkout)
```
1. Admin klik tombol checkout untuk parking transaction
2. Sistem update:
   - Status transaksi menjadi 'completed'
   - quota_used berkurang -1
   - available_quota bertambah +1
```

### 3️⃣ Saat Payment Ditolak (Reject)
```
1. Admin reject parking payment
2. Sistem cek apakah transaksi sebelumnya 'paid':
   - Jika ya: quota_used berkurang -1
   - Jika tidak: Tidak ada perubahan quota
```

---

## 🛠️ Method Baru di Model `ParkingFee`

### Attributes (Accessor)
```php
// Mendapatkan kwota yang tersedia
$parkingFee->available_quota
// Return: capacity - quota_used

// Mendapatkan persentase penggunaan kwota
$parkingFee->quota_usage_percentage
// Return: (quota_used / capacity) * 100
```

### Methods
```php
// Cek apakah kwota tersedia
$parkingFee->hasAvailableQuota($amount = 1)
// Return: boolean

// Menambah quota_used (saat payment created)
$parkingFee->incrementQuota($amount = 1)
// Return: boolean

// Mengurangi quota_used (saat checkout/reject)
$parkingFee->decrementQuota($amount = 1)
// Return: boolean
```

---

## 🌐 API Endpoints Baru

### 1. Checkout Parking
**Route:** `POST /payment/parking/checkout/{id}`

**Request:**
```json
{
  "id": 123  // ID parking transaction
}
```

**Response Success:**
```json
{
  "success": true,
  "message": "Parking checked out successfully. Quota has been released."
}
```

**Response Error:**
```json
{
  "success": false,
  "message": "Only paid parking can be checked out"
}
```

### 2. Get Parking Fees (Updated)
**Route:** `GET /payment/parking/fees/{propertyId}`

**Response:**
```json
[
  {
    "idrec": 1,
    "parking_type": "car",
    "fee": 5000,
    "capacity": 50,
    "quota_used": 35,
    "available_quota": 15,
    "quota_percentage": 70
  },
  {
    "idrec": 2,
    "parking_type": "motorcycle",
    "fee": 2000,
    "capacity": 100,
    "quota_used": 45,
    "available_quota": 55,
    "quota_percentage": 45
  }
]
```

---

## 🎨 Tampilan UI

### Parking Fee Management Table
Menampilkan informasi kwota dengan:
- **Available / Total**: Kwota tersedia / Kapasitas total
- **In Use**: Jumlah kwota yang sedang digunakan
- **Progress Bar**: Visual indikator penggunaan kwota
  - 🟢 Hijau: 0-69% (Aman)
  - 🟡 Kuning: 70-89% (Perhatian)
  - 🔴 Merah: 90-100% (Penuh)
- **Persentase**: Persentase penggunaan kwota

---

## 📊 Contoh Skenario

### Skenario 1: Property Baru (Auto-Created Parking Fee)
```
Property: Kost Baru XYZ
Status: Belum ada parking fee

✅ User membuat parking payment pertama kali
→ Sistem auto-create parking fee dengan capacity = 0 (unlimited)
→ Parking payment berhasil dibuat
→ Message: "Parking payment added successfully. Parking fee auto-created with unlimited capacity (0)."

✅ User bisa terus membuat parking payment tanpa batas
✅ Admin bisa set capacity di Parking Fee Management kapan saja
```

### Skenario 2: Property dengan Unlimited Parking
```
Property: Kost Mahasiswa ABC
Parking Type: Car
Capacity: 0 (unlimited)
Quota Used: 15

✅ User bisa terus membuat parking payment
✅ quota_used tidak bertambah (karena unlimited)
✅ Message: "This property has unlimited parking (no quota limit)."
```

### Skenario 3: Property dengan Kwota Terbatas
```
Property: Kost Mahasiswa ABC
Parking Type: Car
Capacity: 10
Quota Used: 7
Available Quota: 3

✅ User bisa membuat parking payment → quota_used jadi 8
✅ User bisa membuat 2 parking payment lagi
❌ User ke-11 tidak bisa membuat parking payment (quota penuh)
```

### Skenario 4: Property dengan Kwota Motor Terpisah
```
Property: Kost Mahasiswa ABC

Car:
- Capacity: 10
- Quota Used: 8
- Available: 2

Motorcycle:
- Capacity: 50
- Quota Used: 30
- Available: 20

✅ Kwota mobil dan motor terpisah
✅ User bisa parkir motor meskipun parkir mobil hampir penuh
```

### Skenario 5: Checkout Mengembalikan Kwota
```
Before Checkout:
- Quota Used: 10
- Available: 0

After Checkout:
- Quota Used: 9
- Available: 1

✅ Setelah checkout, user baru bisa parkir
```

---

## ⚠️ Validasi & Error Handling

### 1. Kwota Penuh
```
Error: "Parking quota is full for Car. Available: 0, Capacity: 10"
```

### 2. Checkout Non-Paid Transaction
```
Error: "Only paid parking can be checked out"
```

### 3. Decrement Quota Melebihi Batas
```
- quota_used tidak bisa negatif
- Method decrementQuota() return false jika gagal
```

---

## 🔐 Security & Authorization

### Site User Access Control
- Site user hanya bisa melihat parking fee dari property mereka
- Site user hanya bisa membuat parking payment untuk property mereka
- Implementasi di:
  ```php
  $user = Auth::user();
  if ($user->isSite() && $user->property_id) {
      $query->where('property_id', $user->property_id);
  }
  ```

---

## 🧪 Testing Checklist

- [ ] Create parking payment dengan quota tersedia
- [ ] Create parking payment saat quota penuh (harus error)
- [ ] Checkout parking payment (quota bertambah)
- [ ] Reject paid parking payment (quota bertambah)
- [ ] Reject pending parking payment (quota tidak berubah)
- [ ] Tampilan UI menampilkan quota dengan benar
- [ ] Progress bar berubah warna sesuai persentase
- [ ] Site user hanya melihat quota property mereka

---

## 📝 Migration

File: `database/migrations/2026_02_11_160709_add_quota_tracking_to_parking_fee_table.php`

```php
Schema::table('m_parking_fee', function (Blueprint $table) {
    $table->integer('quota_used')->default(0)->after('capacity');
});
```

Untuk rollback:
```bash
php artisan migrate:rollback --step=1
```

---

## 🚀 Deployment

1. **Jalankan Migration:**
   ```bash
   php artisan migrate
   ```

2. **Clear Cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

3. **Set Initial Quota:**
   - Secara default `quota_used` = 0 untuk semua parking fee
   - Jika ada data lama, perlu recalculate quota_used:
   ```sql
   UPDATE m_parking_fee pf
   SET quota_used = (
       SELECT COUNT(*)
       FROM t_parking_fee_transaction pft
       WHERE pft.parking_fee_id = pf.idrec
       AND pft.transaction_status = 'paid'
       AND pft.status = 1
   );
   ```

---

## 📞 Support

Jika ada pertanyaan atau masalah, hubungi tim developer.

**Last Updated:** 2026-02-11
**Version:** 1.0.0
