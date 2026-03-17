<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SidebarItem;
use App\Models\Permission;

class DoorLockSidebarSeeder extends Seeder
{
    public function run(): void
    {
        // Create permission if not exists
        $permission = Permission::firstOrCreate(['name' => 'view_door_locks']);

        // Find the Rooms/Units parent sidebar item
        $roomsParent = SidebarItem::where('name', 'Rooms/Units')
            ->whereNull('route')
            ->first();

        if (!$roomsParent) {
            $this->command->warn('Rooms/Units parent sidebar item not found. Skipping.');
            return;
        }

        // Insert Door Lock sidebar item (after Master Facilities, order 3)
        SidebarItem::firstOrCreate(
            ['route' => 'door-locks.index'],
            [
                'name'          => 'Door Lock',
                'permission_id' => $permission->id,
                'parent_id'     => $roomsParent->id,
                'order'         => 3,
            ]
        );

        $this->command->info('Door Lock sidebar item and permission seeded successfully.');
    }
}
