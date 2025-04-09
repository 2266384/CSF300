<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Single Test User
        User::factory()->create([
            'name' => 'User Test',
            'email' => 'user@example.com',
            'is_admin' => false,
        ]);

        // Single Admin User
        User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);
    }
}
