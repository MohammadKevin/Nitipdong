<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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

        // Rate Limiter untuk polling status pembayaran
        RateLimiter::for('payment-polling', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        // Rate Limiter untuk autentikasi API (Login & Register: maksimal 5 attempt / menit)
        RateLimiter::for('api-auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('auth-login', function (Request $request) {
            $email = (string) $request->input('email', '');
            return Limit::perMinute(5)->by($email . '|' . $request->ip());
        });

        RateLimiter::for('auth-register', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('otp-resend', function (Request $request) {
            return Limit::perMinute(3)->by($request->user()?->id ?: $request->ip());
        });

        // Rate Limiter untuk checkout & pembuatan order (cegah spamming pesanan)
        RateLimiter::for('order-checkout', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
