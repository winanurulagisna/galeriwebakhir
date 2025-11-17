<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register helper files
        $this->app->singleton('ImageHelper', function () {
            return new \App\Helpers\ImageHelper();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load helper files
        require_once app_path('Helpers/ImageHelper.php');
    }
}
