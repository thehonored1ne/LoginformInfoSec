<?php

namespace App\Services;

use App\Models\UserModel;
use App\Services\JwtService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthService
{
    public function __construct(private JwtService $jwtService) {}
    public function login(array $data, $request): array|string|false
    {
        $userOrError = $this->validateCredentials($data, $request, 'login');

        if (is_string($userOrError)) {
            return $userOrError; // Rate limit message or false equivalent
        }

        if (!$userOrError) {
            return false;
        }

        // Success — generate JWT for web login instead of traditional session
        $token = $this->jwtService->generateToken($userOrError);

        return [
            'user' => $userOrError,
            'token' => $token
        ];
    }

    public function apiLogin(array $data, $request): array|string
    {
        $userOrError = $this->validateCredentials($data, $request, 'api-login');

        if (is_string($userOrError)) {
            return $userOrError; // Rate limit message
        }

        if (!$userOrError) {
            return "Invalid credentials.";
        }

        // Success — generate JWT
        $token = $this->jwtService->generateToken($userOrError);

        return [
            'user' => $userOrError,
            'token' => $token
        ];
    }

    private function validateCredentials(array $data, $request, string $rateLimitPrefix): UserModel|string|false
    {
        $email    = strtolower(trim($data['email']));
        $password = $data['password'];
        $key      = $rateLimitPrefix . ':' . $email . '|' . $request->ip();

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

        return $user;
    }

    public function register(array $data): UserModel
    {
        $email    = strtolower(trim($data['email']));
        $password = $data['password'];
        $salt     = bin2hex(random_bytes(16));

        return UserModel::create([
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