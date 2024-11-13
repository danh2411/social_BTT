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
            'Controllers',
            'Config',
            'Console',
            'Models',
            'Requests',
            'Routes',
            'Middleware',
            'Migrations',
            'Providers'
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


        $this->info("Module {$moduleName} đã được tạo thành công!");
    }
}
