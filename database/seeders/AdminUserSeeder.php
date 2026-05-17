<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD');

        if (empty($password)) {
            try {
                $password = 'Adm_' . bin2hex(random_bytes(8));
            } catch (\Exception $e) {
                $password = 'admin1234';
            }
            $this->command->info('No ADMIN_PASSWORD set — generated a password for you.');
        }

        $this->command->info('Creating/updating admin user: ' . $email);

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrator',
                'password' => Hash::make($password),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        try {
            Role::firstOrCreate(['name' => 'admin']);
            if (! $user->hasRole('admin')) {
                $user->assignRole('admin');
            }
        } catch (\Throwable $e) {
            $this->command->warn('Could not assign role: ' . $e->getMessage());
        }

        $this->command->info('Admin user created/updated. Email: ' . $email);
        $this->command->line('Password: ' . $password);
    }
}

