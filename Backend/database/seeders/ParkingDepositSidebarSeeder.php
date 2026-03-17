<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SidebarItem;
use App\Models\Permission;

class ParkingDepositSidebarSeeder extends Seeder
{
    public function run()
    {
        // Create permissions if they don't exist
        $permissionNames = [
            'view_deposit_fees',
            'view_parking_fees',
            'view_parking_payments',
        ];

        foreach ($permissionNames as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        $permissions = Permission::all()->pluck('id', 'name')->toArray();

        // Find the Properties parent sidebar item
        $propertiesParent = SidebarItem::where('name', 'Properties')
            ->whereNull('route')
            ->first();

        if ($propertiesParent) {
            // Add Deposit Fee Management under Properties
            SidebarItem::firstOrCreate(
                ['route' => 'deposit-fees.index'],
                [
                    'name' => 'Deposit Fee Management',
                    'permission_id' => $permissions['view_deposit_fees'] ?? null,
                    'parent_id' => $propertiesParent->id,
                    'order' => 3,
                ]
            );

            // Add Parking Fee Management under Properties
            SidebarItem::firstOrCreate(
                ['route' => 'parking-fees.index'],
                [
                    'name' => 'Parking Fee Management',
                    'permission_id' => $permissions['view_parking_fees'] ?? null,
                    'parent_id' => $propertiesParent->id,
                    'order' => 4,
                ]
            );
        }

        // Find the Financial parent sidebar item
        $financialParent = SidebarItem::where('name', 'Financial')
            ->whereNull('route')
            ->first();

        if ($financialParent) {
            // Add Parking Payments under Financial (after Payments, before Refunds)
            SidebarItem::firstOrCreate(
                ['route' => 'admin.parking-payments.index'],
                [
                    'name' => 'Parking Payments',
                    'permission_id' => $permissions['view_parking_payments'] ?? null,
                    'parent_id' => $financialParent->id,
                    'order' => 2,
                ]
            );

            // Update Refunds order to 3
            SidebarItem::where('route', 'admin.refunds.index')->update(['order' => 3]);
            // Update Reports order to 4
            SidebarItem::where('name', 'Reports')
                ->where('parent_id', $financialParent->id)
                ->update(['order' => 4]);
        }

        $this->command->info('Parking & Deposit sidebar items and permissions seeded successfully.');
    }
}
