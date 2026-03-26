<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::controller(AuthController::class)->group(function () {

    Route::get('/', 'showLogin')->name('login');
    Route::get('/register', 'showRegister')->name('register');
    
    // Admin Only
    Route::get('/home', 'showHomeScreen')->name('home')->middleware(['jwt.auth', 'role:admin']);
    
    // User Only
    Route::get('/dashboard', 'showUserHomeScreen')->name('user.home')->middleware(['jwt.auth', 'role:user']);

    Route::post('/login', 'authenticate')->name('login.post');
    Route::post('/register', 'storeRegister')->name('register.store')->middleware('register.limit');
    Route::post('/logout', 'logout')->name('logout');
    
    // JWT API Routes
    Route::post('/api/login', 'apiLogin')->name('api.login');
    Route::get('/api/profile', 'apiProfile')->name('api.profile')->middleware('jwt.auth');
});

use App\Http\Controllers\PasswordResetController;

Route::controller(PasswordResetController::class)->group(function () {
    Route::get('/forgot-password', 'showForgotPassword')->name('password.request');
    Route::post('/forgot-password', 'sendResetLinkEmail')->name('password.email');
    Route::get('/reset-password/{token}', 'showResetPassword')->name('password.reset');
    Route::post('/reset-password', 'resetPassword')->name('password.update');
});