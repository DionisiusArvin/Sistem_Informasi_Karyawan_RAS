<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

class ViteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (app()->environment('local')) {
            Vite::useDevServerUrl(
                request()->getSchemeAndHttpHost() . ':5173'
            );
        }
    }
}
