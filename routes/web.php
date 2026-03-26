<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::controller(AuthController::class)->group(function () {

    Route::get('/', 'showLogin')->name('login');
    Route::get('/register', 'showRegister')->name('register');
    Route::get('/home', 'showHomeScreen')->name('home')->middleware('jwt.auth');
    Route::post('/login', 'authenticate')->name('login.post');
    Route::post('/register', 'storeRegister')->name('register.store')->middleware('register.limit');
    Route::post('/logout', 'logout')->name('logout');
    
    // JWT API Routes
    Route::post('/api/login', 'apiLogin')->name('api.login');
    Route::get('/api/profile', 'apiProfile')->name('api.profile')->middleware('jwt.auth');
});