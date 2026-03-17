<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mulai transaksi untuk menjaga konsistensi data
        DB::beginTransaction();

        try {
            $userId = 1;
            $adminId = 1;
            $now = now();

            // Validasi: Cek apakah user dengan ID 1 ada
            $userExists = DB::table('users')->where('id', $userId)->exists();

            if (!$userExists) {
                throw new \Exception("User dengan ID {$userId} tidak ditemukan!");
            }

            // Validasi: Cek apakah permission 1-25 ada
            $existingPermissions = DB::table('permissions')
                ->whereBetween('id', [1, 25])
                ->pluck('id')
                ->toArray();

            if (count($existingPermissions) < 25) {
                $missing = array_diff(range(1, 25), $existingPermissions);
                $this->command->warn("Warning: Beberapa permission tidak ditemukan: " . implode(', ', $missing));
            }

            // Siapkan data untuk di-insert
            $dataToInsert = [];

            for ($i = 1; $i <= 25; $i++) {
                // Skip jika permission tidak ada
                if (!in_array($i, $existingPermissions)) {
                    continue;
                }

                $dataToInsert[] = [
                    'user_id' => $userId,
                    'permission_id' => $i,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Insert data menggunakan upsert untuk menghindari duplikasi
            if (!empty($dataToInsert)) {
                DB::table('role_permission')->upsert(
                    $dataToInsert,
                    ['user_id', 'permission_id'], // Unique keys
                    ['updated_by', 'updated_at']  // Columns to update on duplicate
                );

                $count = count($dataToInsert);
                $this->command->info("✅ Berhasil menambahkan {$count} permission untuk user ID {$userId}");
                $this->command->info("📋 Permission yang ditambahkan: ID " . implode(', ', array_column($dataToInsert, 'permission_id')));
            } else {
                $this->command->info("⚠️  Tidak ada data permission yang ditambahkan.");
            }

            // Commit transaksi
            DB::commit();
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();

            $this->command->error("❌ Error: " . $e->getMessage());
            Log::error('UserPermissionSeeder failed: ' . $e->getMessage());
        }
    }
}
