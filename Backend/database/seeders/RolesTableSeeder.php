<?php

namespace Database\Seeders;

use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Laravel\Jetstream\OwnerRole;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = Carbon::now();

        // Roles normal (tanpa ID khusus)
        $defaultRoles = [
            'Super Admin',
            'Owner site',
            'Manager site',
            'Finance site',
            'Front Office', 
            'Owner HO',
            'Manager HO',
            'Finance HO',
        ];

        foreach ($defaultRoles as $role) {
            Role::firstOrCreate(
                ['name' => $role],
                ['created_at' => $timestamp, 'updated_at' => $timestamp]
            );
        }

        // Roles dengan ID khusus (164–166)
        $customRoles = [
            164 => 'Creative',
            165 => 'Administrator',
            166 => 'Finance',
        ];

        foreach ($customRoles as $id => $name) {
            Role::updateOrCreate(
                ['id' => $id],
                [
                    'name'       => $name,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]
            );
        }
    }
}
