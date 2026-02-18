<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::updateOrCreate(
            ['email' => 'admin@hotel.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@hotel.com',
                'password' => Hash::make('admin123'),
                'phone' => '+1 555-0001',
                'address' => '123 Admin Street',
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Create Test User
        User::updateOrCreate(
            ['email' => 'user@hotel.com'],
            [
                'name' => 'Test User',
                'email' => 'user@hotel.com',
                'password' => Hash::make('user123'),
                'phone' => '+1 555-0002',
                'address' => '456 User Avenue',
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Users seeded successfully!');
    }
}
