<?php

namespace App\Services;

use App\Models\UserModel;
use App\Services\JwtService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthService
{
    public function __construct(private JwtService $jwtService) {}
    
    public function login(array $data, $request, string $prefix = 'login'): array|string
    {
        $userOrError = $this->validateCredentials($data, $request, $prefix);

        if (is_string($userOrError)) {
            return $userOrError; // Should not happen often as middleware usually blocks first
        }

        if (!$userOrError) {
            return "Invalid email or password.";
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