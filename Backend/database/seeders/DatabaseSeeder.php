<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {

        $this->call([
            AccountAdminSeeder::class,
            GlobalTitleSeeder::class,
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            SidebarItemsTableSeeder::class,
            RolesAndPermissionsSeeder::class,
            PropertySystemSeeder::class,
            // MPromoBannerSeeder::class,
            PropertyFacilitySeeder::class,
            RoomFacilitySeeder::class,
            DashboardWidgetsSeeder::class,
            RoleDashboardWidgetsSeeder::class,
            // ChatSampleDataSeeder::class,
            ParkingDepositSidebarSeeder::class,
            ParkingFeeTransactionSeeder::class,
        ]);
    }
}
