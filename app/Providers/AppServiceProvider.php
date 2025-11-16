<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {

    }

    public function boot(): void
    {
        $this->loadJsonTranslationsFromSubdirectories();
    }

    protected function loadJsonTranslationsFromSubdirectories(): void
    {
        $locales = ['en', 'vi'];
        
        foreach ($locales as $locale) {
            $jsonPath = resource_path("lang/{$locale}/{$locale}.json");
            
            if (File::exists($jsonPath)) {
                // Add JSON path to Laravel's translation loader
                app('translator')->getLoader()->addJsonPath(dirname($jsonPath));
            }
        }
    }
}
