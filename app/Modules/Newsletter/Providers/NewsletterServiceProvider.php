<?php

namespace App\Modules\Newsletter\Providers;
use App\Indexes\Newsletter;
use App\Modules\Newsletter\Services\Interfaces\NewsletterServiceInterface;
use App\Modules\Newsletter\Services\NewsletterService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Modules\Newsletter\Repositories\Interfaces\NewsletterRepository as NewsletterRepositoryInterface;
use App\Modules\Newsletter\Repositories\NewsletterRepository;


class NewsletterServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'user';
    protected string $controllerNamespace = 'App\Modules\User\Http\Controllers';

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->registerApiRoutes($this->controllerNamespace, __DIR__ . '/../Routes/api.php');

        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');


        $this->app->bind(NewsletterRepositoryInterface::class, function () {
            return new NewsletterRepository(new Newsletter());
        });

        $this->app->bind(NewsletterServiceInterface::class, NewsletterService::class);
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
