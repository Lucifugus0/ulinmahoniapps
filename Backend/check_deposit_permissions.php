<?php

/**
 * Script to check and diagnose deposit sidebar permission issues
 * Run this via: php artisan tinker < check_deposit_permissions.php
 */

echo "=== CHECKING DEPOSIT PERMISSIONS ===\n\n";

// Get current logged in user (adjust user ID if needed)
echo "Enter User ID to check: ";
$userId = 1; // Change this to the user ID you want to check

$user = \App\Models\User::find($userId);

if (!$user) {
    echo "User with ID {$userId} not found!\n";
    exit;
}

echo "Checking permissions for: {$user->first_name} {$user->last_name} (ID: {$user->id})\n";
echo "Role: " . ($user->role ? $user->role->name : 'No Role') . "\n\n";

// Check if user has permissions
$userPermissions = \DB::table('role_permission')
    ->where('user_id', $user->id)
    ->pluck('permission_id')
    ->toArray();

echo "User has " . count($userPermissions) . " permissions assigned.\n\n";

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

echo "=== PERMISSION CHECK RESULTS ===\n\n";

$missingPermissions = [];

foreach ($requiredPermissions as $permName => $description) {
    $permission = \App\Models\Permission::where('name', $permName)->first();

    if (!$permission) {
        echo "⚠️  Permission '{$permName}' NOT FOUND in database!\n";
        echo "    Description: {$description}\n\n";
        continue;
    }

    $hasPermission = in_array($permission->id, $userPermissions);

    if ($hasPermission) {
        echo "✅ {$permName} (ID: {$permission->id})\n";
        echo "    Description: {$description}\n\n";
    } else {
        echo "❌ {$permName} (ID: {$permission->id}) - MISSING!\n";
        echo "    Description: {$description}\n\n";
        $missingPermissions[] = $permission->id;
    }
}

if (count($missingPermissions) > 0) {
    echo "\n=== MISSING PERMISSIONS ===\n";
    echo "User is missing " . count($missingPermissions) . " required permissions.\n\n";

    echo "To fix this issue, please:\n";
    echo "1. Go to: Settings → Role & Permission\n";
    echo "2. Select the user: {$user->first_name} {$user->last_name}\n";
    echo "3. Click 'Manage Access Rights'\n";
    echo "4. Make sure to check these permissions:\n\n";

    foreach ($requiredPermissions as $permName => $description) {
        $permission = \App\Models\Permission::where('name', $permName)->first();
        if ($permission && in_array($permission->id, $missingPermissions)) {
            echo "   - {$permName}: {$description}\n";
        }
    }

    echo "\n5. Click 'Save Access Rights'\n";
} else {
    echo "\n✅ All required permissions are assigned!\n";
    echo "If sidebar still not showing, try:\n";
    echo "1. Clear browser cache (Ctrl+Shift+R)\n";
    echo "2. Logout and login again\n";
    echo "3. Check if user status is active: php artisan tinker then run User::find({$userId})->status\n";
}

echo "\n=== END OF DIAGNOSTIC ===\n";
