<?php

namespace App\Services;

use App\Models\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthService
{
    public function login(array $data, $request): bool|string
    {
        $email    = strtolower(trim($data['email']));
        $password = $data['password'];
        $key      = 'login:' . $email . '|' . $request->ip();

        // Check rate limit
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return "Too many login attempts. Please try again in {$seconds} seconds.";
        }

        $user = UserModel::where('email', $email)->first();

        if (!$user) {
            RateLimiter::hit($key, 60);
            return false;
        }

        $hashedInput = hash('sha256', $password . $user->salt);

        if ($hashedInput !== $user->password) {
            RateLimiter::hit($key, 60);
            return false;
        }

        // Success — clear the limiter
        RateLimiter::clear($key);
        Auth::login($user);
        $request->session()->regenerate();

        return true;
    }

    public function register(array $data): void
    {
        $email    = strtolower(trim($data['email']));
        $password = $data['password'];
        $salt     = bin2hex(random_bytes(16));

        UserModel::create([
            'name'     => explode('@', $email)[0],
            'email'    => $email,
            'password' => hash('sha256', $password . $salt),
            'salt'     => $salt,
        ]);
    }

    public function logout($request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}