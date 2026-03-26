<?php

namespace App\Services;

use App\Models\UserModel;

class JwtService
{
    private string $secret;

    public function __construct()
    {
        // Use Laravel's app key as the secret, remove 'base64:' prefix if present
        $key = config('app.key', 'default-secret-key-that-should-be-changed');
        $this->secret = str_replace('base64:', '', $key);
    }

    public function generateToken(UserModel $user): string
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        $payload = json_encode([
            'sub' => $user->id,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 2) // 2 hours expiration
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public function verifyToken(string $token)
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        list($header, $payload, $signature) = $parts;

        $validSignature = hash_hmac('sha256', $header . "." . $payload, $this->secret, true);
        $base64UrlValidSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($validSignature));

        if (!hash_equals($base64UrlValidSignature, $signature)) {
            return false;
        }

        $payloadData = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);

        if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
            return false;
        }

        return $payloadData;
    }
}
