<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private JwtService $jwtService
    ) {}

    // --- View Methods ---

    public function showLogin()
    {
        return view('pages.extends-login');
    }

    public function showRegister()
    {
        return view('pages.extends-register');
    }

    public function showHomeScreen()
    {
        return view('pages.extends-home');
    }

    public function showUserHomeScreen()
    {
        return view('pages.user-dashboard');
    }

    // --- Logic Methods ---

    public function authenticate(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated(), $request);

        if ($result === false || is_string($result)) {
            return back()->withErrors([
                'email' => is_string($result) ? $result : 'Invalid email or password.',
            ])->withInput();
        }

        $user = $result['user'];
        $redirectRoute = $user->role === 'admin' ? 'home' : 'user.home';

        // Drop the JWT in an HTTP-only cookie lasting 120 minutes (2 hours).
        return redirect()->route($redirectRoute)->withCookie(cookie('jwt_token', $result['token'], 120));
    }

    public function storeRegister(RegisterRequest $request)
    {
        try {
            $user = $this->authService->register($request->validated());
            $token = $this->jwtService->generateToken($user);
        } catch (\Exception $e) {
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }

        $redirectRoute = $user->role === 'admin' ? 'home' : 'user.home';

        // Instantly log them in by dropping the JWT into a cookie upon registration
        return redirect()->route($redirectRoute)->withCookie(cookie('jwt_token', $token, 120));
    }

    public function logout(Request $request)
    {
        // For JWT Web sessions, just forget the browser cookie.
        return redirect('/')->withoutCookie('jwt_token');
    }

    public function apiLogin(LoginRequest $request)
    {
        $result = $this->authService->apiLogin($request->validated(), $request);

        if (is_string($result)) {
            return response()->json(['error' => $result], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => $result['user'],
            'token' => $result['token']
        ], 200);
    }

    public function apiProfile(Request $request)
    {
        return response()->json([
            'message' => 'Profile data retrieved successfully',
            'user' => \Illuminate\Support\Facades\Auth::user()
        ], 200);
    }
}