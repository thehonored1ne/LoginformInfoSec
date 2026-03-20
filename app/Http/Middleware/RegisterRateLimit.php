<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RegisterRateLimit
{
    public function handle(Request $request, Closure $next)
    {
        $key = 'register:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many registration attempts. Try again in {$seconds} seconds.",
            ])->withInput();
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }
}