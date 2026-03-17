<?php

namespace App\Providers;

use App\Services\ExcelService;
use Illuminate\Support\ServiceProvider;

class ExcelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('excel', function ($app) {
            return new ExcelService();
        });

        // Register singleton if needed
        $this->app->singleton(ExcelService::class, function ($app) {
            return new ExcelService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
