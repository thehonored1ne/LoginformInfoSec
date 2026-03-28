<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\JwtService;
use App\Models\UserModel;

class GuestMiddleware
{
    private JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Handle an incoming request.
     * Redirect authenticated users away from 'guest' routes like login/register.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('jwt_token') ?? $request->bearerToken();

        if ($token) {
            $payload = $this->jwtService->verifyToken($token);
            if ($payload) {
                $user = UserModel::find($payload['sub']);
                if ($user) {
                    // Already logged in! Redirect to their dashboard.
                    $route = $user->role === 'admin' ? 'admin.dashboard' : 'user.dashboard';
                    return redirect()->route($route);
                }
            }
        }

        return $next($request);
    }
}
