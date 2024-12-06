<?php

namespace App\Modules\Brand\Providers;

use App\Indexes\Brands;
use App\Modules\Brand\Repositories\Elasticsearch\BrandRepository;
use App\Modules\Brand\Repositories\Elasticsearch\Interfaces\BrandRepositoryInterface;
use App\Modules\Brand\Services\BrandService;
use App\Modules\Brand\Services\Interfaces\BrandServiceInterface;
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

        $this->app->bind(BrandServiceInterface::class, BrandService::class);
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
