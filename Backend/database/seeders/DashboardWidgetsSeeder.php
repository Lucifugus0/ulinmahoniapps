<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DashboardWidget;

class DashboardWidgetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $widgets = [
            // Kategori: Stats (Statistik Booking)
            [
                'name' => 'Konfirmasi Booking (Hari Ini)',
                'slug' => 'booking_today',
                'description' => 'Widget untuk menampilkan jumlah booking hari ini',
                'category' => 'stats',
                'icon' => 'fas fa-calendar-day',
                'order' => 1,
                'is_active' => 1
            ],
            [
                'name' => 'Check-In Stats',
                'slug' => 'booking_checkin',
                'description' => 'Widget untuk menampilkan statistik check-in',
                'category' => 'stats',
                'icon' => 'fas fa-arrow-up',
                'order' => 2,
                'is_active' => 1
            ],
            [
                'name' => 'Check-Out Stats',
                'slug' => 'booking_checkout',
                'description' => 'Widget untuk menampilkan statistik check-out',
                'category' => 'stats',
                'icon' => 'fas fa-arrow-down',
                'order' => 3,
                'is_active' => 1
            ],
            [
                'name' => 'Daftar Check-In Hari Ini',
                'slug' => 'checkin_list',
                'description' => 'Widget untuk menampilkan daftar check-in hari ini',
                'category' => 'stats',
                'icon' => 'fas fa-list',
                'order' => 4,
                'is_active' => 1
            ],
            [
                'name' => 'Daftar Check-Out H-3',
                'slug' => 'checkout_list',
                'description' => 'Widget untuk menampilkan daftar check-out H-3',
                'category' => 'stats',
                'icon' => 'fas fa-list-alt',
                'order' => 5,
                'is_active' => 1
            ],

            // Kategori: Finance (Keuangan)
            [
                'name' => 'Pendapatan Hari Ini',
                'slug' => 'finance_today_revenue',
                'description' => 'Widget untuk menampilkan pendapatan hari ini',
                'category' => 'finance',
                'icon' => 'fas fa-money-bill-wave',
                'order' => 7,
                'is_active' => 1
            ],
            [
                'name' => 'Pendapatan Bulan Ini',
                'slug' => 'finance_monthly_revenue',
                'description' => 'Widget untuk menampilkan pendapatan bulan ini',
                'category' => 'finance',
                'icon' => 'fas fa-chart-line',
                'order' => 6,
                'is_active' => 1
            ],
            [
                'name' => 'Pembayaran Tertunda',
                'slug' => 'finance_pending_payments',
                'description' => 'Widget untuk menampilkan pembayaran yang tertunda',
                'category' => 'finance',
                'icon' => 'fas fa-hourglass-half',
                'order' => 7,
                'is_active' => 1
            ],
            [
                'name' => 'Tingkat Pembayaran',
                'slug' => 'finance_payment_success_rate',
                'description' => 'Widget untuk menampilkan tingkat keberhasilan pembayaran',
                'category' => 'finance',
                'icon' => 'fas fa-check-circle',
                'order' => 8,
                'is_active' => 1
            ],
            [
                'name' => 'Metode Pembayaran',
                'slug' => 'finance_payment_methods',
                'description' => 'Widget untuk menampilkan breakdown metode pembayaran',
                'category' => 'finance',
                'icon' => 'fas fa-credit-card',
                'order' => 9,
                'is_active' => 1
            ],
            [
                'name' => 'Ringkasan Cash Flow',
                'slug' => 'finance_cash_flow',
                'description' => 'Widget untuk menampilkan ringkasan cash flow',
                'category' => 'finance',
                'icon' => 'fas fa-exchange-alt',
                'order' => 10,
                'is_active' => 0
            ],
            [
                'name' => 'Transaksi Terbaru',
                'slug' => 'finance_recent_transactions',
                'description' => 'Widget untuk menampilkan transaksi terbaru',
                'category' => 'finance',
                'icon' => 'fas fa-receipt',
                'order' => 11,
                'is_active' => 0
            ],

            // Kategori: Rooms (Informasi Kamar)
            [
                'name' => 'Ketersediaan Kamar',
                'slug' => 'rooms_availability',
                'description' => 'Widget untuk menampilkan ketersediaan kamar',
                'category' => 'rooms',
                'icon' => 'fas fa-door-open',
                'order' => 14,
                'is_active' => 1
            ],
            [
                'name' => 'Detail Kamar Terisi',
                'slug' => 'rooms_occupied_details',
                'description' => 'Widget untuk menampilkan detail kamar yang sedang terisi',
                'category' => 'rooms',
                'icon' => 'fas fa-bed',
                'order' => 12,
                'is_active' => 1
            ],
            [
                'name' => 'Riwayat Okupansi',
                'slug' => 'rooms_occupancy_history',
                'description' => 'Widget untuk menampilkan riwayat okupansi kamar',
                'category' => 'rooms',
                'icon' => 'fas fa-chart-area',
                'order' => 13,
                'is_active' => 1
            ],
            [
                'name' => 'Breakdown Tipe Kamar',
                'slug' => 'rooms_type_breakdown',
                'description' => 'Widget untuk menampilkan breakdown berdasarkan tipe kamar',
                'category' => 'rooms',
                'icon' => 'fas fa-th-large',
                'order' => 14,
                'is_active' => 1
            ],
            [
                'name' => 'Laporan Per Property',
                'slug' => 'rooms_property_report',
                'description' => 'Widget untuk menampilkan laporan per property',
                'category' => 'rooms',
                'icon' => 'fas fa-building',
                'order' => 15,
                'is_active' => 1
            ],

            // Kategori: Reports (Laporan)
            [
                'name' => 'Grafik Penjualan 30 Hari',
                'slug' => 'report_sales_chart',
                'description' => 'Widget untuk menampilkan grafik penjualan 30 hari terakhir',
                'category' => 'reports',
                'icon' => 'fas fa-chart-bar',
                'order' => 16,
                'is_active' => 1
            ],
            [
                'name' => 'Trend Durasi Sewa',
                'slug' => 'report_rental_duration',
                'description' => 'Widget untuk menampilkan trend durasi sewa',
                'category' => 'reports',
                'icon' => 'fas fa-calendar-alt',
                'order' => 17,
                'is_active' => 1
            ],
            [
                'name' => 'Revenue Per Kamar',
                'slug' => 'report_revenue_per_room',
                'description' => 'Widget untuk menampilkan revenue per kamar yang terisi',
                'category' => 'reports',
                'icon' => 'fas fa-dollar-sign',
                'order' => 18,
                'is_active' => 0
            ]
        ];

        foreach ($widgets as $widget) {
            DashboardWidget::updateOrCreate(
                ['slug' => $widget['slug']],
                $widget
            );
        }
    }
}
