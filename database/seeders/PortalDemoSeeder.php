<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Extra demo accounts for manual QA across guest + host portals.
 * Passwords are plain here so the User model "hashed" cast stores bcrypt correctly.
 *
 * Full cross-role QA data (Elapse QA Demo Inn, wishlist, messages, wallet, loyalty)
 * is applied by WorkflowDemoSeeder after bookings are seeded.
 */
class PortalDemoSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'demo.guest@elapse.test'],
            [
                'name' => 'Demo Guest',
                'password' => 'password',
                'role' => 'user',
                'status' => 'active',
                'phone' => '+919876543210',
                'address' => 'Demo City, India',
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'demo.host@elapse.test'],
            [
                'name' => 'Demo Host',
                'password' => 'password',
                'role' => 'user',
                'status' => 'active',
                'phone' => '+919811122233',
                'address' => 'Demo Host HQ',
            ]
        );

        $this->command->info('Portal demo users: demo.guest@elapse.test / password, demo.host@elapse.test / password');
    }
}
