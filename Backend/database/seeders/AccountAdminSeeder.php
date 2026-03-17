<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AccountAdminSeeder extends Seeder
{
    public function run()
    {
        User::insert([
            // ===============================
            // Administrator (Full Access)
            // ===============================
            [
                'first_name' => 'Admin',
                'last_name' => 'System',
                'username' => 'admin',
                'email' => 'admin_tsno@gmail.com',
                'password' => bcrypt('DigitaLL24$$'),
                'role_id' => 165, // Administrator (Full Access)
                'is_admin' => 1,
                'user_type' => 0, // tambahkan ini
                'property_id' => null, // tambahkan ini
                'status' => 1,
                'created_at' => now(),
            ],
            [
                'first_name' => 'HO Administrator',
                'last_name' => 'Team',
                'username' => 'ho_admin',
                'email' => 'ho-administrator@ulinmahoni.com',
                'password' => bcrypt('5uAtAAQWgd9QcEQFYe'),
                'role_id' => 165, // Administrator (Full Access)
                'is_admin' => 1,
                'user_type' => 0, // tambahkan ini
                'property_id' => null, // tambahkan ini
                'status' => 1,
                'created_at' => now(),
            ],

            // ===============================
            // Manager HO (Role 1)
            // ===============================
            [
                'first_name' => 'User',
                'last_name' => 'Standard',
                'username' => 'user',
                'email' => 'user_tsno@gmail.com',
                'password' => bcrypt('DigitaLL24$$'),
                'role_id' => 1, // Manager HO
                'is_admin' => 1,
                'user_type' => 0, // tambahkan ini
                'property_id' => null, // tambahkan ini
                'status' => 1,
                'created_at' => now(),
            ],

            // ===============================
            // Manager Hotel (Role 2)
            // ===============================
            [
                'first_name' => 'Vincent',
                'last_name' => '',
                'username' => 'vincent',
                'email' => 'vincent.code7@gmail.com',
                'password' => bcrypt('DigitaLL24$$'),
                'role_id' => 2, // Manager Hotel
                'is_admin' => 1,
                'user_type' => 0, // tambahkan ini
                'property_id' => null, // tambahkan ini
                'status' => 1,
                'created_at' => now(),
            ],

            // ===============================
            // Site Admin (Role 3)
            // ===============================
            [
                'first_name' => 'Purchasing',
                'last_name' => 'Staff',
                'username' => 'purchasing',
                'email' => 'purchasing_tsno@gmail.com',
                'password' => bcrypt('DigitaLL24$$'),
                'role_id' => 3, // Site Admin
                'is_admin' => 1,
                'user_type' => 0, // tambahkan ini
                'property_id' => null, // tambahkan ini
                'status' => 1,
                'created_at' => now(),
            ],

            // ===============================
            // Receptionist (Role 4)
            // ===============================
            [
                'first_name' => 'Farhan',
                'last_name' => '',
                'username' => 'farhan',
                'email' => 'm.farhanshihab11@gmail.com',
                'password' => bcrypt('DigitaLL24$$'),
                'role_id' => 4, // Receptionist
                'is_admin' => 1,
                'user_type' => 0, // tambahkan ini
                'property_id' => null, // tambahkan ini
                'status' => 1,
                'created_at' => now(),
            ],

            // ===============================
            // Housekeeping (Role 5)
            // ===============================
            [
                'first_name' => 'Farhans',
                'last_name' => '',
                'username' => 'farhans299',
                'email' => 'farhans29@gmail.com',
                'password' => bcrypt('DigitaLL24$$'),
                'role_id' => 5, // Housekeeping
                'is_admin' => 1,
                'user_type' => 0, // tambahkan ini
                'property_id' => null, // tambahkan ini
                'status' => 1,
                'created_at' => now(),
            ],

            // ===============================
            // Role 6 (Custom)
            // ===============================
            [
                'first_name' => 'Hadrian',
                'last_name' => '',
                'username' => 'hadrian',
                'email' => 'hadriannaufal10@gmail.com',
                'password' => bcrypt('DigitaLL24$$'),
                'role_id' => 6,
                'is_admin' => 1,
                'user_type' => 0, // tambahkan ini
                'property_id' => null, // tambahkan ini
                'status' => 1,
                'created_at' => now(),
            ],

            // ===============================
            // Role 7 (Custom)
            // ===============================
            [
                'first_name' => 'Vin',
                'last_name' => 'User',
                'username' => 'vin123',
                'email' => 'vin123@gmail.com',
                'password' => bcrypt('DigitaLL24$$'),
                'role_id' => 7,
                'is_admin' => 1,
                'user_type' => 0, // tambahkan ini
                'property_id' => null, // tambahkan ini
                'status' => 1,
                'created_at' => now(),
            ],

            // ===============================
            // Creative (Role 164)
            // ===============================
            [
                'first_name' => 'HO Creative',
                'last_name' => 'Team',
                'username' => 'ho_creative',
                'email' => 'ho-creative@ulinmahoni.com',
                'password' => bcrypt('6zwBGfvAjBYGGd99wq'),
                'role_id' => 164, // Creative
                'is_admin' => 1,
                'user_type' => 0, // tambahkan ini
                'property_id' => null, // tambahkan ini
                'status' => 1,
                'created_at' => now(),
            ],

            // ===============================
            // Finance (Role 166)
            // ===============================
            [
                'first_name' => 'HO Finance',
                'last_name' => 'Team',
                'username' => 'ho_finance',
                'email' => 'ho-finance@ulinmahoni.com',
                'password' => bcrypt('XMDvAP9Xce8g6wy7Yy'),
                'role_id' => 166, // Finance
                'is_admin' => 1,
                'user_type' => 0, // tambahkan ini
                'property_id' => null, // tambahkan ini
                'status' => 1,
                'created_at' => now(),
            ],

            // ===============================
            // HO User (Non-Admin, user_type = 0)
            // Dapat melihat semua property
            // ===============================
            [
                'first_name' => 'Staff',
                'last_name' => 'HO',
                'username' => 'staff_ho',
                'email' => 'staff-ho@ulinmahoni.com',
                'password' => bcrypt('DigitaLL24$$'),
                'role_id' => 1, // Manager HO
                'is_admin' => 0,
                'user_type' => 0, // HO
                'property_id' => null,
                'status' => 1,
                'created_at' => now(),
            ],

            // ===============================
            // Site User (Non-Admin, user_type = 1)
            // Hanya melihat data property_id = 1
            // ===============================
            [
                'first_name' => 'Staff',
                'last_name' => 'Site 1',
                'username' => 'staff_site1',
                'email' => 'staff-site1@ulinmahoni.com',
                'password' => bcrypt('DigitaLL24$$'),
                'role_id' => 4, // Receptionist
                'is_admin' => 0,
                'user_type' => 1, // Site
                'property_id' => 1, // Property ID 1
                'status' => 1,
                'created_at' => now(),
            ],

            // ===============================
            // Site User (Non-Admin, user_type = 1)
            // Hanya melihat data property_id = 2
            // ===============================
            [
                'first_name' => 'Staff',
                'last_name' => 'Site 2',
                'username' => 'staff_site2',
                'email' => 'staff-site2@ulinmahoni.com',
                'password' => bcrypt('DigitaLL24$$'),
                'role_id' => 4, // Receptionist
                'is_admin' => 0,
                'user_type' => 1, // Site
                'property_id' => 2, // Property ID 2
                'status' => 1,
                'created_at' => now(),
            ],
        ]);
    }
}
