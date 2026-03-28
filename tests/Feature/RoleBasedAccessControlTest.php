<?php

use App\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create Admin
    $adminSalt = bin2hex(random_bytes(16));
    $this->admin = UserModel::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => hash('sha256', 'password' . $adminSalt),
        'salt' => $adminSalt,
        'role' => 'admin',
    ]);

    // Create Regular User
    $userSalt = bin2hex(random_bytes(16));
    $this->user = UserModel::create([
        'name' => 'Regular User',
        'email' => 'user@example.com',
        'password' => hash('sha256', 'password' . $userSalt),
        'salt' => $userSalt,
        'role' => 'user',
    ]);
});

test('admin can access admin dashboard but not user dashboard', function () {
    // Login as admin
    $loginResponse = $this->post('/login', [
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);
    
    $token = $loginResponse->getCookie('jwt_token')->getValue();

    // Access admin dashboard (should work)
    $this->withCookie('jwt_token', $token)
        ->get('/admin-dashboard')
        ->assertStatus(200)
        ->assertSee('Admin Dashboard')
        ->assertSee('Overview');

    // Access user dashboard (should redirect to admin dashboard)
    $this->withCookie('jwt_token', $token)
        ->get('/user-dashboard')
        ->assertRedirect('/admin-dashboard');
});

test('regular user can access dashboard but not admin dashboard', function () {
    // Login as user
    $loginResponse = $this->post('/login', [
        'email' => 'user@example.com',
        'password' => 'password',
    ]);
    
    $token = $loginResponse->getCookie('jwt_token')->getValue();

    // Access user dashboard (should work)
    $this->withCookie('jwt_token', $token)
        ->get('/user-dashboard')
        ->assertStatus(200)
        ->assertSee('User Dashboard')
        ->assertSee('Workspace');

    // Access admin home (should redirect to user dashboard)
    $this->withCookie('jwt_token', $token)
        ->get('/admin-dashboard')
        ->assertRedirect('/user-dashboard');
});

test('guest is redirected to login from protected routes', function () {
    $this->get('/admin-dashboard')->assertRedirect('/login');
    $this->get('/user-dashboard')->assertRedirect('/login');
});

test('registration defaults to user role', function () {
    $this->post('/register', [
        'email' => 'newuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = UserModel::where('email', 'newuser@example.com')->first();
    expect($user->role)->toBe('user');
});
