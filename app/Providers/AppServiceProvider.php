<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share Settings to all Views
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $gSettings = \App\Models\Setting::getAll();
            $view->with([
                'gSettings' => $gSettings,
                'appName'   => $gSettings['app_name'] ?? 'Buku Tamu Digital',
                'appOrg'    => $gSettings['app_org'] ?? 'SMKN 2 Jakarta',
                'appLogo'   => isset($gSettings['app_logo']) ? asset('storage/'.$gSettings['app_logo']) : null,
                'appFav'    => isset($gSettings['app_favicon']) ? asset('storage/'.$gSettings['app_favicon']) : null,
            ]);
        });
    }
}
