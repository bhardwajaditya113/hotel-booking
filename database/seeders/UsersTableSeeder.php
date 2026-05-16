<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Idempotent so tests can re-run DatabaseSeeder after a shared migrated database.
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);

        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'User',
                'password' => '111',
                'role' => 'user',
                'status' => 'active',
            ]
        );
    }
}
