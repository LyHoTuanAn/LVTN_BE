<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load admin translations from JSON to Laravel translation system
        $this->loadAdminTranslations();
    }

    /**
     * Load admin translations from JSON file to Laravel translation system
     */
    protected function loadAdminTranslations(): void
    {
        $jsonPath = resource_path('lang/translations.json');
        
        if (!File::exists($jsonPath)) {
            return;
        }

        $translations = json_decode(File::get($jsonPath), true) ?? [];
        
        // Load translations into Laravel's translation system
        // Use '*' namespace and format key as '*.key' to allow __('key') directly
        foreach ($translations as $en => $vi) {
            // Set for Vietnamese locale
            app('translator')->addLines(['*.' . $en => $vi], 'vi', '*');
            // Set for English locale (return original)
            app('translator')->addLines(['*.' . $en => $en], 'en', '*');
        }
    }
}
