<?php

namespace paz\Elasticsearch\Providers;

use Illuminate\Support\ServiceProvider;

class ElasticsearchServiceProvider extends ServiceProvider
{
    public function boot() {
        $this->publishConfig();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/laravel-elasticsearch.php', 'laravel-elasticsearch');
    }

    protected function publishConfig()
    {
        if (! function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        $this->publishes([
            __DIR__ . '/../../config/laravel-elasticsearch.php' => config_path('laravel-elasticsearch.php'),
        ], 'config');
    }
}
