<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\UserModel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserModel>
 */
class NewUserFactory extends Factory
{
    protected $model = UserModel::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 1. Generate a unique salt for each fake user
        $salt = bin2hex(random_bytes(16));

        // 2. Hash the default password 'password123' using your manual SHA-256 logic
        $manualHash = hash('sha256', 'password123' . $salt);

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => $manualHash, // Stored as manual SHA-256
            'salt' => $salt,           // Stored unique salt
            'remember_token' => Str::random(10),
        ];
    }
}