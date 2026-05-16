<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Plain password for seeding / `admin:create-user` (see config app.admin_password).
     */
    public static function adminPassword(): string
    {
        return (string) config('app.admin_password');
    }

    /**
     * Create or update the admin user (idempotent).
     */
    public static function syncAdmin(?string $email = null, ?string $name = null, ?string $password = null): User
    {
        $email = $email ?? config('app.admin_email');
        $name = $name ?? config('app.admin_name');
        $password = $password ?? self::adminPassword();

        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'role' => 'admin',
                'status' => 'active',
            ]
        );
    }

    public function run(): void
    {
        self::syncAdmin();
        $this->command?->info('Admin user synced: '.config('app.admin_email'));
    }
}
