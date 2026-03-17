<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\DashboardWidget;

class RoleDashboardWidgetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all widgets by slug
        $widgets = DashboardWidget::all()->keyBy('slug');

        // Define role widget assignments
        $roleWidgets = [
            'Administrator' => 'all',
            'Super Admin' => 'all', // Meskipun ada auto-access, tetap assign untuk konsistensi
            'Owner HO' => 'all',
            'Owner site' => 'all',

            'Manager HO' => [
                // Stats
                'booking_today', 'booking_checkin', 'booking_checkout',
                'checkin_list', 'checkout_list',
                // Finance
                'finance_today_revenue', 'finance_monthly_revenue', 'finance_pending_payments',
                'finance_payment_success_rate', 'finance_payment_methods',
                // Rooms
                'rooms_availability', 'rooms_occupied_details', 'rooms_occupancy_history',
                'rooms_type_breakdown', 'rooms_property_report',
                // Reports
                'report_sales_chart', 'report_rental_duration',
            ],

            'Manager site' => [
                // Stats
                'booking_today', 'booking_checkin', 'booking_checkout',
                'checkin_list', 'checkout_list',
                // Finance (limited)
                'finance_today_revenue', 'finance_monthly_revenue',
                // Rooms
                'rooms_availability', 'rooms_occupied_details', 'rooms_type_breakdown',
            ],

            'Finance HO' => [
                // Stats (limited)
                'booking_today',
                // Finance (all active)
                'finance_today_revenue', 'finance_monthly_revenue', 'finance_pending_payments',
                'finance_payment_success_rate', 'finance_payment_methods',
                // Reports
                'report_sales_chart',
            ],

            'Finance site' => [
                // Stats (limited)
                'booking_today',
                // Finance (limited)
                'finance_today_revenue', 'finance_monthly_revenue', 'finance_payment_methods',
                // Reports
                'report_sales_chart',
            ],

            'Finance' => [ // Finance role (general)
                // Stats (limited)
                'booking_today',
                // Finance (limited)
                'finance_today_revenue', 'finance_monthly_revenue', 'finance_payment_methods',
                // Reports
                'report_sales_chart',
            ],

            'Front Office' => [
                // Stats
                'booking_today', 'booking_checkin', 'booking_checkout',
                'checkin_list', 'checkout_list',
                // Rooms
                'rooms_availability', 'rooms_occupied_details',
            ],

            'Creative' => [
                // Stats (basic)
                'booking_today',
                // Rooms (basic)
                'rooms_availability',
            ],
        ];

        foreach ($roleWidgets as $roleName => $widgetSlugs) {
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                $this->command->warn("Role '{$roleName}' not found, skipping...");
                continue;
            }

            // If 'all', assign all widgets
            if ($widgetSlugs === 'all') {
                $widgetIds = $widgets->pluck('id')->toArray();
            } else {
                // Get specific widget IDs
                $widgetIds = [];
                foreach ($widgetSlugs as $slug) {
                    if (isset($widgets[$slug])) {
                        $widgetIds[] = $widgets[$slug]->id;
                    } else {
                        $this->command->warn("Widget '{$slug}' not found for role '{$roleName}'");
                    }
                }
            }

            // Sync widgets to role (this will remove old assignments and add new ones)
            $role->dashboardWidgets()->sync($widgetIds);

            $this->command->info("Assigned " . count($widgetIds) . " widgets to role '{$roleName}'");
        }

        $this->command->info('Dashboard widget permissions seeded successfully!');
    }
}
