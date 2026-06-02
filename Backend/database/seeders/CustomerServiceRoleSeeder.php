<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the 'Customer Service' role into the roles table.
 * HQ Customer Service users are user_type=0 (HO) with this role assigned.
 */
class CustomerServiceRoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('m_roles')->updateOrInsert(
            ['name' => 'Customer Service'],
            [
                'name' => 'Customer Service',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
