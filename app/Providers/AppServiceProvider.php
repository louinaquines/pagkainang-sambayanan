<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

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
