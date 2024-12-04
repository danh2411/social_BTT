<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModule extends Command
{
    protected $signature = 'make:module {name}';
    protected $description = 'Tạo module mới với cấu trúc đơn giản';

    public function handle()
    {
        $moduleName = $this->argument('name');
        $modulePath = base_path("app/Modules/{$moduleName}");

        // Tạo thư mục chính của module
        File::makeDirectory($modulePath, 0755, true);

        // Tạo các thư mục con
        $directories = [
            'Http/Middleware',
            'Http/Requests',
            'Http/Controllers',
            'Config',
            'Console',
            'Models',
            'Requests',
            'Routes',
            'Migrations',
            'Repositories/Interfaces',
            'Providers',
            'Services/Interfaces',
        ];

        foreach ($directories as $dir) {
            File::makeDirectory("{$modulePath}/{$dir}", 0755, true);
        }
        // Tạo file config API
        $routeFile = "{$modulePath}/Config/config.php";
        File::put($routeFile, "<?php\n\n// config for {$moduleName} module\n");


        // Tạo file route API
        $routeFile = "{$modulePath}/Routes/api.php";
        File::put($routeFile, "<?php\n\n// API routes for {$moduleName} module\n");

        // Tạo file controller mẫu
        $controllerFile = "{$modulePath}/Http/Controllers/{$moduleName}Controller.php";
        File::put($controllerFile, "<?php\n\nnamespace App\Modules\\{$moduleName}\Http\Controllers;\n\nuse App\Http\Controllers\Controller;\n\nclass {$moduleName}Controller extends Controller\n{\n    //\n}\n");

        // Tạo file model mẫu
        $modelFile = "{$modulePath}/Models/{$moduleName}.php";
        File::put($modelFile, "<?php\n\nnamespace App\Modules\\{$moduleName}\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass {$moduleName} extends Model\n{\n    protected \$fillable = [];\n}\n");

        // Tạo file Providers API
        $routeFile = "{$modulePath}/Providers/" . $moduleName . "ServiceProvider.php";
        File::put($routeFile, "<?php\n\n// Providers for {$moduleName} module\n");

        // Tạo file Providers API
        $routeFile = "{$modulePath}/Services/Interfaces/" . $moduleName . "ServiceInterface.php";
        File::put($routeFile, "<?php\n\nnamespace App\Modules\\{$moduleName}\Services\Interfaces;\n\ninterface {$moduleName}ServiceInterface\n{\n    //\n}\n");
        $routeFile = "{$modulePath}/Services/" . $moduleName . "Service.php";
        File::put($routeFile, "<?php\n\nnamespace App\Modules\\{$moduleName}\Services;\n\nclass {$moduleName}Service extends {$moduleName}ServiceInterface\n{\n    //\n}\n");

        $this->info("Module {$moduleName} đã được tạo thành công!");
    }
}
