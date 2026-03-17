<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class GlobalTitleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            $globalTitle = DB::table('global_title')->where('key', 'OS Name')->value('mark');
            View::share('globalTitle', $globalTitle);
        } catch (\Exception $e) {
            View::share('globalTitle', config('app.name'));
        }
    }
}
