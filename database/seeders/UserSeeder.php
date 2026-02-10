<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin/IQA Account
        User::create([
            'name' => '',
            'email' => '@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'ADMIN',
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);

        // Create Dean (ACTIVE)
        User::create([
            'name' => 'Jennifer Gorumba',
            'email' => 'jennifer@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'DEAN',
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);

        // Create Task Force
        User::create([
            'name' => 'Geryl Cataraja',
            'email' => 'geryl@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'TASK FORCE',
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Ruby Mary G. Encenzo',
            'email' => 'ruby@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'TASK FORCE',
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);

        // Create Internal Assessor (ACTIVE)
        User::create([
            'name' => 'Levi Esmero',
            'email' => 'levi@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'INTERNAL ASSESSOR',
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);

        // Create Accreditor (ACTIVE)
        User::create([
            'name' => 'Janet Aclao',
            'email' => 'janet@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'ACCREDITOR',
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);
        
        // Create 10 Task Force users
        User::factory()->count(10)->create([
            'user_type' => 'TASK FORCE',
            'status' => 'Active',
        ]);

        // Create 10 Internal Assessor users
        User::factory()->count(10)->create([
            'user_type' => 'INTERNAL ASSESSOR',
            'status' => 'Active',
        ]);
    }
}
