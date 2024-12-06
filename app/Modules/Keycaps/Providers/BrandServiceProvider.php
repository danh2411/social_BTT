<?php

namespace App\Modules\Brands\Providers;

use App\Indexes\Brands;
use App\Indexes\Newsletter;
use App\Indexes\Resources;
use App\Modules\Brands\Repositories\BrandRepository;
use App\Modules\Brands\Repositories\Interfaces\BrandRepository as BrandRepositoryInterface;

use App\Modules\Brands\Services\KeyCapService;
use App\Modules\Brands\Services\Interfaces\KeyCapServiceInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BrandServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'resources';
    protected string $controllerNamespace = 'App\Modules\Resources\Http\Controllers';

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->registerApiRoutes($this->controllerNamespace, __DIR__ . '/../Routes/api.php');

        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');



        $this->app->bind(BrandRepositoryInterface::class, function () {
            return new BrandRepository(new Brands());
        });

        $this->app->bind(KeyCapServiceInterface::class, KeyCapService::class);
    }

    /**
     * Register API routes for the module.
     */
    protected function registerApiRoutes($controllerNamespace, $routeFilePath): void
    {

        Route::middleware(['api'])
            ->namespace($controllerNamespace)
            ->group($routeFilePath);
    }
}
