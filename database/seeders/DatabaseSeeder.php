<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// Import your custom model
use App\Models\UserModel;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create your own account so you always have a login ready
        // We manually define the salt and hash here to be 100% sure of the password
        $adminSalt = bin2hex(random_bytes(16));
        
        UserModel::create([
            'name' => 'Dion Areglo',
            'email' => 'admin@test.com',
            'password' => hash('sha256', 'password123' . $adminSalt),
            'salt' => $adminSalt,
            'email_verified_at' => now(),
        ]);

        // 2. Create 10 random users using your NewUserFactory
        // This uses the 'password123' logic we put in the factory definition
        UserModel::factory(10)->create();
    }
}