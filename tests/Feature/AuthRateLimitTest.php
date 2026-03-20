<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthRateLimitTest extends TestCase
{
    use RefreshDatabase; // adds this

    public function test_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
                'email'    => 'test@test.com',
                'password' => 'wrongpassword',
            ]);
        }

        $response->assertStatus(302);
        $this->assertStringContainsString(
            'Too many login attempts',
            session('errors')->first('email')
        );
    }

    public function test_register_is_rate_limited(): void
    {
        for ($i = 0; $i < 4; $i++) {
            $response = $this->post('/register', [
                'email'    => "test{$i}@test.com",
                'password' => 'password123',
            ]);
        }

        $response->assertStatus(302);
        $this->assertStringContainsString(
            'Too many registration attempts',
            session('errors')->first('email')
        );
    }
}