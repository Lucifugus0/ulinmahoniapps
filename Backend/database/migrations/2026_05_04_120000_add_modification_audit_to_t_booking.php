<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the audit-trail columns to t_booking that the new Modify Booking flow uses.
 *
 * The Modify Booking flow clones the active t_booking row each time an admin changes
 * check_in / check_out / paid_at on the linked t_transactions. The clone carries the
 * modification metadata so the chain of changes can be walked via previous_booking_id.
 *
 *   modified_at         when the modification was applied
 *   modified_by         which admin applied it (FK users.id)
 *   modification_type   short tag — 'date_change', 'payment_date_change', or 'date+payment'
 *                       so the history view can group / colorize by type without parsing notes
 *   modification_notes  optional free-form note the admin entered as reason
 *
 * These four fields stay NULL on rows that are NOT modification clones (initial bookings,
 * renewals, room transfers) so existing queries are unaffected.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('t_booking', function (Blueprint $table) {
            $table->timestamp('modified_at')->nullable()->after('room_changed_by');
            $table->unsignedBigInteger('modified_by')->nullable()->after('modified_at');
            $table->string('modification_type', 50)->nullable()->after('modified_by');
            $table->text('modification_notes')->nullable()->after('modification_type');
        });
    }

    public function down(): void
    {
        Schema::table('t_booking', function (Blueprint $table) {
            $table->dropColumn(['modified_at', 'modified_by', 'modification_type', 'modification_notes']);
        });
    }
};
