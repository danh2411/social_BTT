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
        if (!File::exists($modulePath)) {
            File::makeDirectory($modulePath, 0755, true);
        }

        // Tạo các thư mục con
        $directories = [
            'Http/Middleware',
            'Http/Requests',
            'Http/Controllers',
            'Config',
            'Console',
            'Models',
            'Repositories/Interfaces',
            'Repositories/Eloquent',
            'Routes',
            'Migrations',
            'Providers',
            'Services/Interfaces',
            'Services',
        ];

        foreach ($directories as $dir) {
            $dirPath = "{$modulePath}/{$dir}";
            if (!File::exists($dirPath)) {
                File::makeDirectory($dirPath, 0755, true);
            }
        }

        // Tạo file config module
        $configFile = "{$modulePath}/Config/config.php";
        File::put($configFile, "<?php\n\n// Config for {$moduleName} module\nreturn [];\n");

        // Tạo file route API
        $routeFile = "{$modulePath}/Routes/api.php";
        File::put($routeFile, "<?php\n\n// API routes for {$moduleName} module\n");

        // Tạo file controller mẫu
        $controllerFile = "{$modulePath}/Http/Controllers/{$moduleName}Controller.php";
        File::put($controllerFile, "<?php\n\nnamespace App\Modules\\{$moduleName}\Http\Controllers;\n\nuse App\Http\Controllers\Controller;\n\nclass {$moduleName}Controller extends Controller\n{\n    // Controller logic\n}\n");

        // Tạo file model mẫu
        $modelFile = "{$modulePath}/Models/{$moduleName}.php";
        File::put($modelFile, "<?php\n\nnamespace App\Modules\\{$moduleName}\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass {$moduleName} extends Model\n{\n    protected \$fillable = [];\n}\n");

        // Tạo file ServiceProvider
        $serviceProviderFile = "{$modulePath}/Providers/{$moduleName}ServiceProvider.php";
        File::put($serviceProviderFile, "<?php\n\nnamespace App\Modules\\{$moduleName}\Providers;\n\nuse Illuminate\Support\ServiceProvider;\n\nclass {$moduleName}ServiceProvider extends ServiceProvider\n{\n    public function register()\n    {\n        // Register services\n    }\n\n    public function boot()\n    {\n        // Boot services\n    }\n}\n");

        // Tạo file ServiceInterface
        $serviceInterfaceFile = "{$modulePath}/Services/Interfaces/{$moduleName}ServiceInterface.php";
        File::put($serviceInterfaceFile, "<?php\n\nnamespace App\Modules\\{$moduleName}\Services\Interfaces;\n\ninterface {$moduleName}ServiceInterface\n{\n    // Define methods here\n}\n");

        // Tạo file Service class
        $serviceFile = "{$modulePath}/Services/{$moduleName}Service.php";
        File::put($serviceFile, "<?php\n\nnamespace App\Modules\\{$moduleName}\Services;\n\nuse App\Modules\\{$moduleName}\Services\Interfaces\\{$moduleName}ServiceInterface;\n\nclass {$moduleName}Service implements {$moduleName}ServiceInterface\n{\n    // Implement methods here\n}\n");

        // Tạo file Migration mẫu
        $migrationFile = "{$modulePath}/Migrations/{$moduleName}_create_table.php";
        File::put($migrationFile, "<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nclass {$moduleName}CreateTable extends Migration\n{\n    public function up()\n    {\n        Schema::create('{$moduleName}s', function (Blueprint \$table) {\n            \$table->id();\n            \$table->timestamps();\n        });\n    }\n\n    public function down()\n    {\n        Schema::dropIfExists('{$moduleName}s');\n    }\n}\n");

        // Tạo file Repository Interface
        $repositoryInterfaceFile = "{$modulePath}/Repositories/Interfaces/{$moduleName}RepositoryInterface.php";
        File::put($repositoryInterfaceFile, "<?php\n\nnamespace App\Modules\\{$moduleName}\Repositories\Interfaces;\n\ninterface {$moduleName}RepositoryInterface\n{\n    // Define repository methods\n}\n");

        // Tạo file Repository Eloquent
        $repositoryFile = "{$modulePath}/Repositories/Eloquent/{$moduleName}Repository.php";
        File::put($repositoryFile, "<?php\n\nnamespace App\Modules\\{$moduleName}\Repositories\Eloquent;\n\nuse App\Modules\\{$moduleName}\Repositories\Interfaces\\{$moduleName}RepositoryInterface;\n\nclass {$moduleName}Repository implements {$moduleName}RepositoryInterface\n{\n    // Implement repository methods here\n}\n");

        // Thông báo khi module đã được tạo thành công
        $this->info("Module {$moduleName} đã được tạo thành công!");
    }
}
