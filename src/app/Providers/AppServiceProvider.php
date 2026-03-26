<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Trust Railway's proxy so HTTPS is detected correctly
        Request::setTrustedProxies(
            ['127.0.0.1', '10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16'],
            Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO
        );

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            try {
                $emergencyMode = DB::table('settings')
                    ->where('key', 'emergency_mode')
                    ->value('value');
                $emergencyModeActive = $emergencyMode === '1' || $emergencyMode === 'true';
            } catch (\Exception $e) {
                $emergencyModeActive = false;
            }

            $view->with('emergencyModeActive', $emergencyModeActive);
        });
    }
}