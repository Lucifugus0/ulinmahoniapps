<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up()
    {
        $now = Carbon::now();

        // Add promo banners permission
        $permissionId = DB::table('permissions')->insertGetId([
            'name' => 'view_promo_banners',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Assign to Super Admin (user_id = 1) and other admin roles
        $adminUserIds = [1, 7]; // Add more user IDs as needed

        foreach ($adminUserIds as $userId) {
            // Check if user exists before assigning permission
            $userExists = DB::table('users')->where('id', $userId)->exists();
            if ($userExists) {
                DB::table('role_permission')->insert([
                    'user_id' => $userId,
                    'permission_id' => $permissionId,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'created_by' => 1,
                    'updated_by' => 1,
                ]);
            }
        }
    }

    public function down()
    {
        $permission = DB::table('permissions')->where('name', 'view_promo_banners')->first();

        if ($permission) {
            DB::table('role_permission')->where('permission_id', $permission->id)->delete();
            DB::table('permissions')->where('id', $permission->id)->delete();
        }
    }
};
