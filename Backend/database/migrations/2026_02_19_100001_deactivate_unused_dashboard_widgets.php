<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Nonaktifkan widget dashboard yang belum dirender di dashboard.blade.php.
     * Slug: finance_cash_flow, finance_recent_transactions, report_revenue_per_room
     */
    public function up(): void
    {
        DB::table('dashboard_widgets')
            ->whereIn('slug', [
                'finance_cash_flow',
                'finance_recent_transactions',
                'report_revenue_per_room',
            ])
            ->update(['is_active' => 0]);
    }

    public function down(): void
    {
        DB::table('dashboard_widgets')
            ->whereIn('slug', [
                'finance_cash_flow',
                'finance_recent_transactions',
                'report_revenue_per_room',
            ])
            ->update(['is_active' => 1]);
    }
};
