<?php

namespace App\Modules\Resources\Providers;

use App\Indexes\Resources;
use App\Modules\Resources\Repositories\Elasticsearch\Interfaces\ESResourceRepository as ResourceRepositoryInterface;
use App\Modules\Resources\Repositories\Mysql\Interfaces\MysqlResourceRepository as MysqlResourceRepositoryInterface;
use App\Modules\Resources\Repositories\Elasticsearch\ESResourceRepository;
use App\Modules\Resources\Repositories\Mysql\MysqlResourceRepository;
use App\Modules\Resources\Services\Interfaces\ResourceServiceInterface;
use App\Modules\Resources\Services\ResourceService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ResourceServiceProvider extends ServiceProvider
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



        $this->app->bind(ResourceRepositoryInterface::class, function () {
            return new ESResourceRepository(new Resources());
        });
        $this->app->bind(MysqlResourceRepositoryInterface::class, function () {
            return new MysqlResourceRepository(new \App\Modules\Resources\Models\Resources());
        });

        $this->app->bind(ResourceServiceInterface::class, ResourceService::class);
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
