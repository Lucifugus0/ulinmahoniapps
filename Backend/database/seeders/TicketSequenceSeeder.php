<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the t_ticket_sequences table with one row per property (using initial)
 * plus one for HQ. Each row tracks the last assigned ticket/broadcast number
 * for that scope, enabling atomic auto-increment numbering.
 */
class TicketSequenceSeeder extends Seeder
{
    public function run(): void
    {
        /** Get all property initials from the properties table */
        $properties = DB::table('m_properties')->pluck('initial')->filter()->unique();

        foreach ($properties as $initial) {
            /** Create ticket sequence for this property */
            DB::table('t_ticket_sequences')->updateOrInsert(
                ['scope' => $initial, 'sequence_type' => 'ticket'],
                ['last_number' => 0]
            );
            /** Create broadcast sequence for this property */
            DB::table('t_ticket_sequences')->updateOrInsert(
                ['scope' => $initial, 'sequence_type' => 'broadcast'],
                ['last_number' => 0]
            );
        }

        /** Create HQ sequences for ticket and broadcast */
        DB::table('t_ticket_sequences')->updateOrInsert(
            ['scope' => 'HQ', 'sequence_type' => 'ticket'],
            ['last_number' => 0]
        );
        DB::table('t_ticket_sequences')->updateOrInsert(
            ['scope' => 'HQ', 'sequence_type' => 'broadcast'],
            ['last_number' => 0]
        );
    }
}
