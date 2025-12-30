<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <--- 1. WAJIB DITAMBAHKAN

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
        // 2. Tambahkan logika ini untuk memaksa HTTPS
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
    }
}