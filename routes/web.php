<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Grouping all routes that use the AuthController for cleaner code.
Route::controller(AuthController::class)->group(function () {

    // Page View Routes (GET). These return the Blade templates for each page.
    Route::get('/', 'showLogin')->name('login');
    Route::get('/register', 'showRegister')->name('register');

    // Protected Route. This 'auth' middleware prevents guests from seeing the homepage.
    Route::get('/home', 'showHomeScreen')->name('home')->middleware('auth');

    // Logic/Action Routes (POST) — rate limited.
    Route::post('/login', 'authenticate')->name('login.post');
    Route::post('/register', 'storeRegister')->name('register.store');

    // Logout doesn't need rate limiting.
    Route::post('/logout', 'logout')->name('logout');
});