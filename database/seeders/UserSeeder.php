<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Dean (ACTIVE)
        User::create([
            'name' => 'CGS Dean',
            'email' => 'cgsdean@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'ADMIN',
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);

        // Create Task Force Chair (ACTIVE)
        User::create([
            'name' => 'Janet S. Aclao',
            'email' => 'janet@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'TASK FORCE CHAIR',
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);

        // Create Task Force Member (ACTIVE)
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
            'name' => 'Carol O. Laurente',
            'email' => 'carol@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'INTERNAL ASSESSOR',
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);

        // Create Accreditor (ACTIVE)
        User::create([
            'name' => 'Romil L. Asoque',
            'email' => 'romil@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'ACCREDITOR',
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);
        
        // Create 18 Task Force users (ALL INACTIVE)
        User::factory()->count(18)->create([
            'user_type' => 'TASK FORCE',
            'status' => 'Inactive',
        ]);
    }
}
