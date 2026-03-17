<?php

namespace App\Providers;

use App\Mail\Transports\MailtrapTransport;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

class MailServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->afterResolving('mail.manager', function (MailManager $mailManager) {
            $mailManager->extend('mailtrap', function (array $config = []) {
                return new MailtrapTransport(
                    config('services.mailtrap.api_key'),
                    config('mail.from.address'),
                    config('mail.from.name')
                );
            });
        });
    }
}
