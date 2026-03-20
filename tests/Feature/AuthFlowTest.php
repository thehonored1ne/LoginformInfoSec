<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    private function createUser(string $email = 'test@test.com', string $password = 'password123'): UserModel
    {
        $salt = bin2hex(random_bytes(16));

        return UserModel::create([
            'name'     => explode('@', $email)[0],
            'email'    => $email,
            'password' => hash('sha256', $password . $salt),
            'salt'     => $salt,
        ]);
    }

    private function loginAs(string $email = 'test@test.com', string $password = 'password123')
    {
        return $this->post('/login', [
            'email'    => $email,
            'password' => $password,
        ]);
    }

    // -------------------------------------------------------
    // Login Tests
    // -------------------------------------------------------

    public function test_login_page_is_accessible(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_login_with_correct_credentials(): void
    {
        $this->createUser();

        $this->loginAs()->assertRedirect('/home');
    }

    public function test_login_with_wrong_password(): void
    {
        $this->createUser();

        $this->loginAs('test@test.com', 'wrongpassword')
            ->assertRedirect()
            ->assertSessionHasErrors('email');
    }

    public function test_login_with_nonexistent_email(): void
    {
        $this->loginAs('nobody@test.com', 'password123')
            ->assertRedirect()
            ->assertSessionHasErrors('email');
    }

    public function test_login_with_empty_fields(): void
    {
        $this->post('/login', ['email' => '', 'password' => ''])
            ->assertRedirect()
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_with_invalid_email_format(): void
    {
        $this->post('/login', ['email' => 'notanemail', 'password' => 'password123'])
            ->assertRedirect()
            ->assertSessionHasErrors('email');
    }

    // -------------------------------------------------------
    // Register Tests
    // -------------------------------------------------------

    public function test_register_page_is_accessible(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_register_with_valid_data(): void
    {
        $this->post('/register', [
            'email'                 => 'newuser@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@test.com',
        ]);
    }

    public function test_register_with_duplicate_email(): void
    {
        $this->createUser('existing@test.com');

        $this->post('/register', [
            'email'                 => 'existing@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect()
          ->assertSessionHasErrors('email');
    }

    public function test_register_with_empty_fields(): void
    {
        $this->post('/register', ['email' => '', 'password' => ''])
            ->assertRedirect()
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_register_with_invalid_email_format(): void
    {
        $this->post('/register', [
            'email'    => 'notanemail',
            'password' => 'password123',
        ])->assertRedirect()
          ->assertSessionHasErrors('email');
    }

    // -------------------------------------------------------
    // Logout Tests
    // -------------------------------------------------------

    public function test_logout_redirects_to_login(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/');
    }

    public function test_logout_clears_session(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->post('/logout');

        $this->assertGuest();
    }

    // -------------------------------------------------------
    // Route Protection Tests
    // -------------------------------------------------------

    public function test_guest_cannot_access_home(): void
    {
        $this->get('/home')->assertRedirect('/');
    }

    public function test_authenticated_user_can_access_home(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->get('/home')
            ->assertStatus(200);
    }

    public function test_authenticated_user_redirects_away_from_login(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->get('/')
            ->assertStatus(200);
    }
}