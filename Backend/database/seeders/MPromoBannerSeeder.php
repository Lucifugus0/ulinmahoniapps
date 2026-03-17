<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PromoBanner;
use Carbon\Carbon;

class MPromoBannerSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $data = [
            [
                'idrec' => 1,
                'title' => 'Promo Akhir Tahun',
                'attachment' => null,
                'descriptions' => 101,
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 'admin',
                'updated_by' => 'admin',
                'status' => 1,
            ],
            [
                'idrec' => 2,
                'title' => 'Diskon Spesial Lebaran',
                'attachment' => null,
                'descriptions' => 102,
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 'admin',
                'updated_by' => 'admin',
                'status' => 1,
            ],
            [
                'idrec' => 3,
                'title' => 'Flash Sale',
                'attachment' => null,
                'descriptions' => 103,
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 'marketing',
                'updated_by' => 'marketing',
                'status' => 1,
            ],
            [
                'idrec' => 4,
                'title' => 'Promo Ramadhan',
                'attachment' => null,
                'descriptions' => 104,
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 'system',
                'updated_by' => 'system',
                'status' => 1,
            ],
            [
                'idrec' => 5,
                'title' => 'Voucher Belanja',
                'attachment' => null,
                'descriptions' => 105,
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 'admin',
                'updated_by' => 'admin',
                'status' => 1,
            ],
        ];

        foreach ($data as $item) {
            PromoBanner::create($item);
        }
    }
}
