<?php

namespace App\Providers;

use App\Services\RabbitMQService;
use Illuminate\Support\ServiceProvider;

class RabbitMQServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton(RabbitMQService::class, function ($app) {
            return new RabbitMQService();
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
