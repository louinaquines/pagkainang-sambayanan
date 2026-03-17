<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        
        View::composer('*', function ($view) {
            try {
                $settings = DB::getSchemaBuilder()->hasTable('settings') 
                    ? Setting::first() 
                    : null;
            } catch (\Exception $e) {
                $settings = null;
            }

            $view->with('emergencyModeActive', $settings?->emergency_mode ?? false);
        });
    }
}
