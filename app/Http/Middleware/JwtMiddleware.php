<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\JwtService;
use App\Models\UserModel;
use Illuminate\Support\Facades\Auth;

class JwtMiddleware
{
    private JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        // If no bearer token, check cookies (for web auth)
        if (!$token) {
            $token = $request->cookie('jwt_token');
        }

        if (!$token) {
            // If they are visiting a web page and aren't authenticated, redirect to login
            if (!$request->expectsJson()) {
                return redirect()->route('login');
            }
            return response()->json(['error' => 'Token not provided'], 401);
        }

        $payload = $this->jwtService->verifyToken($token);

        if (!$payload) {
            if (!$request->expectsJson()) {
                return redirect()->route('login')->withErrors(['email' => 'Session expired. Please log in again.']);
            }
            return response()->json(['error' => 'Invalid or expired token'], 401);
        }

        $user = UserModel::find($payload['sub']);

        if (!$user) {
            if (!$request->expectsJson()) {
                return redirect()->route('login');
            }
            return response()->json(['error' => 'User not found'], 401);
        }

        // Set the user in the Auth facade for this request
        Auth::setUser($user);

        return $next($request);
    }
}
