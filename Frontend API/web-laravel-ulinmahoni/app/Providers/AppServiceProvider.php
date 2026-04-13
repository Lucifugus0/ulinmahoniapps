<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register FooterComposer to inject CMS footer data into footer and property pages
        \Illuminate\Support\Facades\View::composer(
            [
                'components.homepage.footer',
                'pages.house.show',
                'pages.house.id.show',
                'pages.house.en.show',
                'pages.house.show-img-square',
            ],
            \App\View\Composers\FooterComposer::class
        );
    }
}
