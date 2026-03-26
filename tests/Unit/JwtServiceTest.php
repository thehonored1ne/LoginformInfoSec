<?php

use App\Services\JwtService;
use App\Models\UserModel;
use Illuminate\Support\Facades\Config;

uses(Tests\TestCase::class);

test('it can generate a valid token', function () {
    // Mock the config key so that JwtService uses a predictable secret
    Config::set('app.key', 'base64:testing-secret-key-1234567890=');

    $service = new JwtService();

    // Create a mock user
    $user = new UserModel();
    $user->id = 1;
    $user->email = 'testing@example.com';

    $token = $service->generateToken($user);

    // Verify token structure
    $parts = explode('.', $token);
    expect(count($parts))->toBe(3);
});

test('it can verify a valid token', function () {
    Config::set('app.key', 'base64:testing-secret-key-1234567890=');

    $service = new JwtService();

    $user = new UserModel();
    $user->id = 55;
    $user->email = 'testing2@example.com';

    $token = $service->generateToken($user);
    $payload = $service->verifyToken($token);

    expect($payload)->toBeArray();
    expect($payload['sub'])->toBe(55);
    expect($payload['email'])->toBe('testing2@example.com');
});

test('it rejects an altered token', function () {
    Config::set('app.key', 'base64:testing-secret-key-1234567890=');

    $service = new JwtService();

    $user = new UserModel();
    $user->id = 1;
    $user->email = 'testing@example.com';

    $token = $service->generateToken($user);

    // Alter the payload
    $parts = explode('.', $token);
    $payloadData = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
    $payloadData['sub'] = 99; // Change user ID
    
    $parts[1] = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payloadData)));
    $alteredToken = implode('.', $parts);

    $payload = $service->verifyToken($alteredToken);

    expect($payload)->toBeFalse();
});
