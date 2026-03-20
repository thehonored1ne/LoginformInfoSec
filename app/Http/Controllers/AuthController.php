<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
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

    // --- Logic Methods ---

    public function authenticate(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated(), $request);

        if ($result !== true) {
            return back()->withErrors([
                'email' => $result ?: 'Invalid email or password.',
            ])->withInput();
        }

        return redirect()->intended('home');
    }

    public function storeRegister(RegisterRequest $request)
    {
        try {
            $this->authService->register($request->validated());
        } catch (\Exception $e) {
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }

        return redirect()->route('login')->with('success', 'Registration successful!');
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return redirect('/');
    }
}