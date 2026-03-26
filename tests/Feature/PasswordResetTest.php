<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordEmail;
use Carbon\Carbon;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $email = 'test@test.com', string $password = 'password123'): UserModel
    {
        $salt = bin2hex(random_bytes(16));

        $email = strtolower(trim($email));
        return UserModel::create([
            'name'     => explode('@', $email)[0],
            'email'    => $email,
            'password' => hash('sha256', $password . $salt),
            'salt'     => $salt,
            'role'     => 'user'
        ]);

    }

    public function test_forgot_password_page_is_accessible()
    {
        $this->get('/forgot-password')->assertStatus(200);
    }

    public function test_reset_link_can_be_requested()
    {
        Mail::fake();
        $user = $this->createUser();

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);

        Mail::assertSent(ResetPasswordEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_reset_link_cannot_be_requested_for_nonexistent_email()
    {
        Mail::fake();

        $response = $this->post('/forgot-password', [
            'email' => 'nobody@test.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');

        Mail::assertNothingSent();
    }

    public function test_reset_password_page_is_accessible_with_valid_token()
    {
        $user = $this->createUser();
        $token = 'valid-token';
        
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $this->get("/reset-password/{$token}?email={$user->email}")
            ->assertStatus(200)
            ->assertSee($user->email);
    }

    public function test_password_can_be_reset_with_valid_token()
    {
        $user = $this->createUser();
        $token = 'valid-token';
        
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('login'));

        $response->assertSessionHas('status');

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);

        // Verify user can login with new password
        $user->refresh();
        $hashedInput = hash('sha256', 'newpassword123' . $user->salt);
        $this->assertEquals($hashedInput, $user->password);
    }

    public function test_password_cannot_be_reset_with_invalid_token()
    {
        $user = $this->createUser();
        
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => 'valid-token',
            'created_at' => Carbon::now(),
        ]);

        $response = $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
    }

    public function test_password_cannot_be_reset_with_expired_token()
    {
        $user = $this->createUser();
        $token = 'expired-token';
        
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now()->subMinutes(61), // Expired
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
    }

    public function test_password_reset_request_is_rate_limited()
    {
        $user = $this->createUser();

        // Perform 3 requests
        for ($i = 0; $i < 3; $i++) {
            $this->post('/forgot-password', ['email' => $user->email]);
        }

        // 4th request should be rate limited
        $response = $this->post('/forgot-password', ['email' => $user->email]);
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Too many reset attempts', session('errors')->first('email'));
    }

    public function test_password_mismatch_fails_reset()
    {
        $user = $this->createUser();
        $token = 'valid-token';
        
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'different-one',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('password');
    }

    public function test_short_password_fails_reset()
    {
        $user = $this->createUser();
        $token = 'valid-token';
        
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('password');
    }

    public function test_password_can_be_reset_with_case_insensitive_email()
    {
        $user = $this->createUser('TestUser@test.com');
        $token = 'valid-token';
        
        DB::table('password_reset_tokens')->insert([
            'email' => strtolower($user->email),
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'TESTUSER@test.com', // Different case
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => strtolower($user->email)]);
    }
}
