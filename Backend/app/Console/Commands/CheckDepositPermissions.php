<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckDepositPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:check-deposit {user_id? : The ID of the user to check} {--fix : Automatically add missing permissions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and optionally fix deposit sidebar permissions for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $autoFix = $this->option('fix');

        // If no user ID provided, show list of users
        if (!$userId) {
            $this->info('Available users:');
            $users = User::where('status', 1)->orderBy('id')->get();

            $tableData = [];
            foreach ($users as $user) {
                $tableData[] = [
                    'ID' => $user->id,
                    'Name' => $user->first_name . ' ' . $user->last_name,
                    'Email' => $user->email,
                    'Role' => $user->role ? $user->role->name : 'No Role',
                ];
            }

            $this->table(['ID', 'Name', 'Email', 'Role'], $tableData);

            $userId = $this->ask('Enter User ID to check');

            if (!$userId) {
                $this->error('User ID is required!');
                return 1;
            }
        }

        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        $this->info("Checking permissions for: {$user->first_name} {$user->last_name} (ID: {$user->id})");
        $this->info("Role: " . ($user->role ? $user->role->name : 'No Role'));
        $this->newLine();

        // Get user's current permissions
        $userPermissions = DB::table('role_permission')
            ->where('user_id', $user->id)
            ->pluck('permission_id')
            ->toArray();

        $this->info("User has " . count($userPermissions) . " permissions assigned.");
        $this->newLine();

        // Required permissions for deposit sidebars
        $requiredPermissions = [
            // For Properties -> Deposit Fee Management
            'view_properties' => 'Required to see Properties menu',
            'view_deposit_fees' => 'Required to see Deposit Fee Management submenu',

            // For Financial -> Payments -> Deposit
            'financial' => 'Required to see Financial section',
            'view_payments' => 'Required to see Payments menu',
            'view_deposit_payments' => 'Required to see Deposit submenu in Payments',
        ];

        $this->info('=== PERMISSION CHECK RESULTS ===');
        $this->newLine();

        $missingPermissions = [];
        $tableData = [];

        foreach ($requiredPermissions as $permName => $description) {
            $permission = Permission::where('name', $permName)->first();

            if (!$permission) {
                $tableData[] = [
                    'Permission' => $permName,
                    'Status' => '⚠️  NOT IN DB',
                    'Description' => $description,
                ];
                continue;
            }

            $hasPermission = in_array($permission->id, $userPermissions);

            if ($hasPermission) {
                $tableData[] = [
                    'Permission' => $permName,
                    'Status' => '✅ HAS',
                    'Description' => $description,
                ];
            } else {
                $tableData[] = [
                    'Permission' => $permName,
                    'Status' => '❌ MISSING',
                    'Description' => $description,
                ];
                $missingPermissions[] = [
                    'name' => $permName,
                    'id' => $permission->id,
                    'description' => $description,
                ];
            }
        }

        $this->table(['Permission', 'Status', 'Description'], $tableData);
        $this->newLine();

        if (count($missingPermissions) > 0) {
            $this->warn("User is missing " . count($missingPermissions) . " required permissions.");
            $this->newLine();

            if ($autoFix) {
                $this->info("Auto-fixing missing permissions...");

                DB::transaction(function () use ($user, $missingPermissions) {
                    foreach ($missingPermissions as $missingPerm) {
                        $exists = DB::table('role_permission')
                            ->where('user_id', $user->id)
                            ->where('permission_id', $missingPerm['id'])
                            ->exists();

                        if (!$exists) {
                            DB::table('role_permission')->insert([
                                'user_id' => $user->id,
                                'permission_id' => $missingPerm['id'],
                                'created_at' => Carbon::now(),
                                'created_by' => 1, // System
                            ]);

                            $this->line("  ✅ Added: {$missingPerm['name']}");
                        }
                    }
                });

                $this->newLine();
                $this->info("✅ All missing permissions have been added!");
                $this->info("Please ask the user to logout and login again to see the changes.");
            } else {
                $this->info("To fix this issue manually:");
                $this->line("1. Go to: Settings → Role & Permission");
                $this->line("2. Select the user: {$user->first_name} {$user->last_name}");
                $this->line("3. Click 'Manage Access Rights'");
                $this->line("4. Make sure to check these missing permissions:");
                $this->newLine();

                foreach ($missingPermissions as $missingPerm) {
                    $this->line("   - {$missingPerm['name']}: {$missingPerm['description']}");
                }

                $this->newLine();
                $this->line("5. Click 'Save Access Rights'");
                $this->newLine();
                $this->info("Or run this command with --fix flag to automatically add missing permissions:");
                $this->line("php artisan permissions:check-deposit {$userId} --fix");
            }
        } else {
            $this->info("✅ All required permissions are assigned!");
            $this->newLine();
            $this->info("If sidebar still not showing, try:");
            $this->line("1. Clear browser cache (Ctrl+Shift+R or Cmd+Shift+R)");
            $this->line("2. Logout and login again");
            $this->line("3. Check if user status is active");
        }

        return 0;
    }
}
