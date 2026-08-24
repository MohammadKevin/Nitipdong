<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Support cPanel / DomaiNesia public_html structure
        if (is_dir(base_path('../public_html'))) {
            $this->app->usePublicPath(realpath(base_path('../public_html')));
        }
    }

    public function boot(): void
    {
        if (app()->environment('production') || str_contains((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        \Illuminate\Pagination\Paginator::useTailwind();
    }
}

