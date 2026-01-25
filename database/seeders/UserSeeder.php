<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Create Admin (ACTIVE)
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'ADMIN',
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);

        // ✅ Create Task Force (INACTIVE)
        User::create([
            'name' => 'Task Force Member',
            'email' => 'taskforce@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'TASK FORCE',
            'status' => 'Inactive',
            'email_verified_at' => now(),
        ]);

        // ✅ Create 18 Task Force users (ALL INACTIVE)
        User::factory()->count(18)->create([
            'user_type' => 'TASK FORCE',
            'status' => 'Inactive',
        ]);
    }
}
