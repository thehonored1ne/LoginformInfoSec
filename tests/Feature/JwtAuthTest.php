<?php

use App\Models\UserModel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can login and receive a jwt cookie', function () {
    // Generate an actual user in the DB rather than mock
    $salt = bin2hex(random_bytes(16));
    $user = UserModel::create([
        'name' => 'Test User',
        'email' => 'test_feature@example.com',
        'password' => hash('sha256', 'supersecret' . $salt),
        'salt' => $salt,
    ]);

    $response = $this->post('/login', [
        'email' => 'test_feature@example.com',
        'password' => 'supersecret',
    ]);

    $response->assertRedirect('/user-dashboard');
    $response->assertCookie('jwt_token');
});

test('a user cannot access admin page without the jwt cookie', function () {
    $response = $this->get('/admin-dashboard');

    // Should redirect back to login
    $response->assertRedirect('/login');
});

test('a user can access their dashboard with valid jwt cookie', function () {
    $salt = bin2hex(random_bytes(16));
    $user = UserModel::create([
        'name' => 'Test User 2',
        'email' => 'test_feature2@example.com',
        'password' => hash('sha256', 'supersecret' . $salt),
        'salt' => $salt,
    ]);

    $loginResponse = $this->post('/login', [
        'email' => 'test_feature2@example.com',
        'password' => 'supersecret',
    ]);

    // Extract cookie from the response
    $cookie = $loginResponse->getCookie('jwt_token');
    
    // Request /dashboard with the cookie
    $homeResponse = $this->withCookie('jwt_token', $cookie->getValue())->get('/user-dashboard');

    $homeResponse->assertStatus(200);
});

test('a user logging out loses the jwt cookie', function () {
    $response = $this->post('/logout');

    $response->assertRedirect('/login');
    
    // Cookie should be expired
    $cookie = $response->headers->getCookies()[0];
    expect($cookie->getName())->toBe('jwt_token');
    expect($cookie->getExpiresTime())->toBeLessThanOrEqual(time());
});

test('api login returns json with token', function () {
    $salt = bin2hex(random_bytes(16));
    $user = UserModel::create([
        'name' => 'API User',
        'email' => 'api_test@example.com',
        'password' => hash('sha256', 'apisecret' . $salt),
        'salt' => $salt,
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'api_test@example.com',
        'password' => 'apisecret',
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure(['message', 'user', 'token']);
});
