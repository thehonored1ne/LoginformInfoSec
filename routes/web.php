<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;

Route::controller(AuthController::class)->group(function () {
    // Navigation routes
    Route::get('/login', 'showLogin')->name('login')->middleware('guest');
    Route::redirect('/', '/login');
    Route::get('/register', 'showRegister')->name('register')->middleware('guest');
    
    // Admin Only
    Route::get('/admin-dashboard', 'showAdminDashboard')->name('admin.dashboard')->middleware(['jwt.auth', 'role:admin']);
    
    // User Only
    Route::get('/user-dashboard', 'showUserDashboard')->name('user.dashboard')->middleware(['jwt.auth', 'role:user']);

    // Login and Register
    Route::post('/login', 'authenticate')->name('login.post')->middleware('secure.throttle:login,5,check');
    Route::post('/register', 'storeRegister')->name('register.store')->middleware('secure.throttle:register,3,hit');
    Route::get('/logout', function() { return redirect()->route('login'); });
    Route::post('/logout', 'logout')->name('logout');
    
    // JWT API Routes
    Route::get('/api/login', function() { return redirect()->route('login'); });
    Route::post('/api/login', 'apiLogin')->name('api.login')->middleware('secure.throttle:login,5,check');
    Route::get('/api/profile', 'apiProfile')->name('api.profile')->middleware('jwt.auth');
});

Route::controller(PasswordResetController::class)->group(function () {
    // Forgot Password route
    Route::get('/forgot-password', 'showForgotPassword')->name('password.request');
    // Forgot pass form route
    Route::post('/forgot-password', 'sendResetLinkEmail')->name('password.email')->middleware('secure.throttle:password-reset,3,hit');
    Route::get('/reset-password/{token}', 'showResetPassword')->name('password.reset');
    Route::post('/reset-password', 'resetPassword')->name('password.update');
});

// Fallback for api Access
Route::fallback(function () {
    if (request()->is('api/*')) {
        return response()->json(['message' => 'API endpoint not found.'], 404);
    }
    return redirect()->route('login');
});

// Fallback for not existent routes
Route::fallback(function () {
    if (request()->is('/*')) {
        return response()->json(['message' => 'Pages not found'], 404);
    }
    return redirect()->route('login');
});