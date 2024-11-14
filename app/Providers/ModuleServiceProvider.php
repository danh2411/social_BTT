<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->modules();
    }

    protected function modules()
    {
        $modules = config('modules.modules', []);
        foreach ($modules as $config) {
            if (isset($config['active']) && $config['active'] === true) {
                unset($config['active']);
                $this->registerModule($config);
            }
        }
    }

    protected function registerModule($configs)
    {
        foreach ($configs as $key => $config) {
            switch ($key) {
                case 'providers':
                    $this->registerProviders($config);
                    break;
                default:
                    break;
            }
        }
    }

    protected function registerProviders(array $providers)
    {

        foreach ($providers as $provider) {
            $this->app->register($provider);
        }
    }
}
