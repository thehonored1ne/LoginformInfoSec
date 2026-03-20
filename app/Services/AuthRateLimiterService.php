<?php

namespace App\Services;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AuthRateLimiterService
{
    public function configureLimiters(): array
    {
        return [
            Limit::perMinute(5)->by(request()->ip()),
            Limit::perMinute(3)->by(request()->input('email')),
        ];
    }
}