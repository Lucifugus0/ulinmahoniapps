<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class VersionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the version helper
        require_once app_path('Helpers/VersionHelper.php');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share version with all views
        view()->share('appVersion', \App\Helpers\VersionHelper::getVersion());
        
        // Register a blade directive
        Blade::directive('version', function () {
            return "<?php echo \\App\\Helpers\\VersionHelper::getVersion(); ?>";
        });
    }
}
