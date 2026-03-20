<?php

namespace App\Providers;

use App\Services\AuthRateLimiterService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthRateLimiterService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function () {
            $service = app(AuthRateLimiterService::class);
            return $service->configureLimiters();
        });
    }
}