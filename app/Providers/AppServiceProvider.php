<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\MaintenanceMode;
use Illuminate\Foundation\MaintenanceModeManager;
use Illuminate\Filesystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Solución para el error "Target class [files] does not exist"
        $this->app->singleton('files', function ($app) {
            return new Filesystem();
        });

        // Mantenimiento
        $this->app->singleton(MaintenanceMode::class, function ($app) {
            return $app->make(MaintenanceModeManager::class);
        });
    }

    public function boot(): void
    {
        //
    }
}
