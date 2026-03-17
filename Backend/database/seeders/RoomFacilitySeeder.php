<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoomFacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('m_room_facility')->insert([
            [
                'facility' => 'AC',
                'description' => 'Pendingin ruangan untuk kenyamanan tamu',                
                'status' => '1',
                'created_by' => '1',                
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'facility' => 'Wi-Fi',
                'description' => 'Akses internet gratis',                
                'status' => '1',
                'created_by' => '1',                
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'facility' => 'TV Kabel',
                'description' => 'TV dengan saluran internasional',                
                'status' => '1',
                'created_by' => '1',                
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'facility' => 'Kamar Mandi Dalam',
                'description' => 'Kamar mandi pribadi dengan shower air panas',                
                'status' => '1',
                'created_by' => '1',                
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'facility' => 'Meja & Kursi',
                'description' => 'Area kerja kecil di dalam kamar',                
                'status' => '1',
                'created_by' => '1',                
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
