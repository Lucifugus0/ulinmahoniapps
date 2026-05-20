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
        // <!-- Bind under 'custom-excel', NOT 'excel'. The maatwebsite/excel package binds 'excel'
        //      in its own service provider; sharing the key made whichever provider registered last
        //      win the container, so MaatwebsiteExcel::download() was resolving to this ExcelService
        //      and throwing a type error on the export object. Keeping a distinct key lets both the
        //      custom PaymentReport exporter and the maatwebsite-based Refund/Booking exporters work. -->
        $this->app->bind('custom-excel', function ($app) {
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
