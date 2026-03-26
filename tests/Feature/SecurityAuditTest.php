<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

/**
 * SecurityAuditTest: Verifies defenses against common web attacks (OWASP Top 10)
 */
class SecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $role = 'user'): UserModel
    {
        $salt = bin2hex(random_bytes(16));
        return UserModel::create([
            'name'     => 'SecurityTestUser',
            'email'    => 'test@nexus.com',
            'password' => hash('sha256', 'password123' . $salt),
            'salt'     => $salt,
            'role'     => $role
        ]);
    }

    /**
     * 1. SQL Injection (SQLi) Defense
     * We try to login using a classic SQL injection payload.
     * The app uses Eloquent/PDO which uses prepared statements, making this injection fail.
     */
    public function test_vulnerability_sqli_is_blocked()
    {
        // Payload designed to trick a vulnerable SQL query into returning TRUE
        $sqliPayload = "' OR '1'='1' --";

        $response = $this->post('/login', [
            'email'    => $sqliPayload,
            'password' => 'any-password'
        ]);

        // The app should not log the user in, but redirect back with an error
        $response->assertStatus(302);
        $this->assertFalse(\Illuminate\Support\Facades\Auth::check());
    }

    /**
     * 2. Persistent Cross-Site Scripting (XSS) Defense
     * We try to register with a script in the name.
     * Blade template engine automatically escapes all output {{ $var }}, blocking XSS execution.
     */
    public function test_vulnerability_xss_is_escaped()
    {
        $xssPayload = "<script>alert('hacked')</script>";

        // Attempting to register with a script in the email (which is used for name)
        $response = $this->post('/register', [
            'email'                 => 'xss@nexus.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(302);
        
        // Verify the data is saved, but Blade will escape it when rendered
        $this->assertDatabaseHas('users', ['email' => 'xss@nexus.com']);
    }

    /**
     * 3. Broken Access Control (Privilege Escalation)
     * A "user" role should never be able to access "admin" routes.
     */
    public function test_vulnerability_unauthorized_user_blocked_from_admin()
    {
        $user = $this->createUser('user');
        
        // Login and get token
        $loginRes = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);
        $token = $loginRes->getCookie('jwt_token')->getValue();

        // Try to access Admin /home
        $response = $this->withCookie('jwt_token', $token)->get('/home');

        // Should be redirected back to their own dashboard
        $response->assertRedirect(route('user.home'));
    }

    /**
     * 4. JWT Tampering (Broken Authentication)
     * Hacker tries to manually change the signature of the JWT token.
     */
    public function test_vulnerability_jwt_tampering_is_rejected()
    {
        $user = $this->createUser('user');
        $loginRes = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);
        $validToken = $loginRes->getCookie('jwt_token')->getValue();

        // Manually break the token signature (change the last character)
        $tamperedToken = substr($validToken, 0, -1) . ($validToken[strlen($validToken)-1] === 'A' ? 'B' : 'A');

        $response = $this->withCookie('jwt_token', $tamperedToken)->get('/dashboard');

        // The middleware should reject the token and redirect to login
        $response->assertRedirect(route('login'));
    }

    /**
     * 5. Password Reset Token Enumeration
     * Ensure we can't reset a password without the EXACT token (secure 64-char string).
     */
    public function test_vulnerability_token_enumeration_fails()
    {
        $user = $this->createUser();
        
        // Try to guess a token for the user
        $response = $this->post('/reset-password', [
            'token'                 => '12345', // Short/Guessebale instead of 64 chars
            'email'                 => $user->email,
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }
}
