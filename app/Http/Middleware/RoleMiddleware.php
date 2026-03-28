<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = Auth::user();

        if (!$user || $user->role !== $role) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            // Redirect based on role if unauthorized
            if ($user && $user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user && $user->role === 'user') {
                return redirect()->route('user.dashboard');
            }

            return redirect()->route('login')->withErrors(['email' => 'Unauthorized access.']);
        }

        return $next($request);
    }
}
