<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
        ]);

        User::factory()->create([
            'name' => 'Bot User 1',
            'email' => 'bot1@example.com',
            'bot' => true,
        ]);

        User::factory()->create([
            'name' => 'Bot User 2',
            'email' => 'bot2@example.com',
            'bot' => true,
        ]);

        User::factory()->create([
            'name' => 'Bot User 3',
            'email' => 'bot3@example.com',
            'bot' => true,
        ]);
    }
}
