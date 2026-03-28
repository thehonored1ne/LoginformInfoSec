<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class SecurityThrottle
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @param  string   $prefix            The prefix for the cache key (e.g. 'login', 'register')
     * @param  int      $maxAttempts       Maximum number of attempts allowed before lockout
     * @param  string   $hitMode           'hit' = record attempt immediately (default), 'check' = only check limit
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $prefix, int $maxAttempts = 5, string $hitMode = 'hit')
    {
        // 1. Generate the key
        // For registration, we only care about the IP to prevent mass account creation.
        // For other actions, we include the email if present to block specific account attacks.
        $email = ($prefix === 'register') ? '' : strtolower(trim($request->input('email', '')));
        $key = $prefix . ':' . ($email ? $email . '|' : '') . $request->ip();

        // 2. Check if the limit is exceeded
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            // Define custom messages per action
            $message = match ($prefix) {
                'login'          => "Too many login attempts. Please try again in {$seconds} seconds.",
                'register'       => "Too many registration attempts. Please try again in {$seconds} seconds.",
                'password-reset' => "Too many reset attempts. Please try again in {$seconds} seconds.",
                default          => "Too many attempts. Please try again in {$seconds} seconds.",
            };

            // Return JSON for API, Redirect for Web
            if ($request->expectsJson()) {
                return response()->json(['error' => $message], 429);
            }

            return back()->withErrors(['email' => $message])->withInput();
        }

        // 3. Pre-emptively hit the limiter (Prevents spamming on slow requests)
        // Set to 'check' if you want to manually hit on failure (like in login).
        if ($hitMode === 'hit') {
            RateLimiter::hit($key, 60);
        }

        return $next($request);
    }
}
