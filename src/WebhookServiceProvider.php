<?php

namespace Samirlama\Webhook;

use Illuminate\Support\ServiceProvider;

class WebhookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        include __DIR__.'/routes.php';
    }
}
