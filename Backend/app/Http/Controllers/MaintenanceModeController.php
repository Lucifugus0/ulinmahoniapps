<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// <!-- Controller to toggle maintenance mode on/off via the global_title table.
//      When maintenance_mode is '1', the Frontend website shows a maintenance landing page
//      and the mobile app shows a maintenance popup.
//      Note: `key` is a MySQL reserved word, so queries use backtick-escaped column name. -->
class MaintenanceModeController extends Controller
{
    /**
     * Show the maintenance mode management page
     */
    public function index()
    {
        return view('pages.settings.maintenance-mode');
    }

    /**
     * Get current maintenance mode status
     */
    public function status()
    {
        // <!-- Use DB::raw for `key` column since it's a MySQL reserved word -->
        $maintenance = DB::table('global_title')
            ->whereRaw('`key` = ?', ['maintenance_mode'])
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'maintenance_mode' => $maintenance ? $maintenance->mark === '1' : false,
            ],
        ]);
    }

    /**
     * Toggle maintenance mode on/off
     */
    public function toggle(Request $request)
    {
        $enabled = $request->boolean('enabled');

        // <!-- Upsert maintenance_mode row in global_title table -->
        $exists = DB::table('global_title')
            ->whereRaw('`key` = ?', ['maintenance_mode'])
            ->exists();

        if ($exists) {
            DB::table('global_title')
                ->whereRaw('`key` = ?', ['maintenance_mode'])
                ->update(['mark' => $enabled ? '1' : '0']);
        } else {
            DB::table('global_title')->insert([
                'idrec' => DB::table('global_title')->max('idrec') + 1,
                'key' => 'maintenance_mode',
                'mark' => $enabled ? '1' : '0',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => $enabled ? 'Maintenance mode enabled' : 'Maintenance mode disabled',
            'data' => [
                'maintenance_mode' => $enabled,
            ],
        ]);
    }
}
