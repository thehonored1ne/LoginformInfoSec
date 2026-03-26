<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;

/**
 * StressTest verifies the application's defensive layers under high pressure
 * and rapid-fire requests.
 */
class StressTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $email = 'admin@nexus.com'): UserModel
    {
        $salt = bin2hex(random_bytes(16));
        return UserModel::create([
            'name'     => 'AdminUser',
            'email'    => strtolower(trim($email)),
            'password' => hash('sha256', 'password123' . $salt),
            'salt'     => $salt,
            'role'     => 'admin'
        ]);
    }

    /**
     * Test logic: Rapidly hitting the login endpoint to check for IP/Email lockout.
     */
    public function test_login_flood_is_blocked_after_5_attempts()
    {
        $email = 'bruteforce@nexus.com';
        
        // 5 attempts should go through (even if they fail)
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $email, 'password' => 'wrong-pass'])
                 ->assertStatus(302);
        }

        // 6th attempt should return a validation error for rate limiting
        $response = $this->post('/login', ['email' => $email, 'password' => 'wrong-pass']);
        $this->assertStringContainsString('Too many login attempts', session('errors')->first('email'));
    }

    /**
     * Test logic: Rapidly creating accounts (bots/spam) should hit the limit quickly.
     */
    public function test_registration_flood_is_blocked_after_3_attempts()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->post('/register', [
                'email' => "bot_test_$i@nexus.com",
                'password' => 'password123',
                'password_confirmation' => 'password123'
            ]);
        }

        // 4th attempt blocked
        $response = $this->post('/register', [
            'email' => 'final_bot@nexus.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        
        $this->assertStringContainsString('Too many registration attempts', session('errors')->first('email'));
    }

    /**
     * Test logic: Multiple password reset link requests should be throttled.
     */
    public function test_password_reset_requests_are_throttled_at_3_attempts()
    {
        $user = $this->createUser();

        // 3 attempts are allowed
        for ($i = 0; $i < 3; $i++) {
            $this->post('/forgot-password', ['email' => $user->email]);
        }

        // 4th attempt blocked
        $response = $this->post('/forgot-password', ['email' => $user->email]);
        
        $this->assertStringContainsString('Too many reset attempts', session('errors')->first('email'));
    }

    /**
     * Test logic: Verifies that multiple valid JWT tokens (concurrent logins) 
     * don't interfere with each other.
     */
    public function test_concurrent_valid_jwt_sessions()
    {
        $user = $this->createUser('nexus_user@test.com');

        // Session 1 Login
        $response1 = $this->post('/login', ['email' => 'nexus_user@test.com', 'password' => 'password123']);
        $token1 = $response1->getCookie('jwt_token')->getValue();

        // Session 2 Login
        $response2 = $this->post('/login', ['email' => 'nexus_user@test.com', 'password' => 'password123']);
        $token2 = $response2->getCookie('jwt_token')->getValue();

        // Both sessions should work independently
        $this->withCookie('jwt_token', $token1)->get('/home')->assertStatus(200);
        $this->withCookie('jwt_token', $token2)->get('/home')->assertStatus(200);
    }
}
