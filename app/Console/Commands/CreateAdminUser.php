<?php

namespace App\Console\Commands;

use Database\Seeders\AdminUserSeeder;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create-user
                            {--email= : Admin email (default: config app.admin_email)}
                            {--name= : Display name (default: config app.admin_name)}
                            {--password= : Plain password (default: ADMIN_PASSWORD env or 111)}
                            {--force : Skip confirmation prompt}';

    protected $description = 'Write the admin user to the database (permanent until DB reset). Safe to re-run: same email is updated, not duplicated.';

    public function handle(): int
    {
        $email = $this->option('email') ?: config('app.admin_email');
        $name = $this->option('name') ?: config('app.admin_name');
        $password = $this->option('password');
        if ($password === null || $password === '') {
            $password = AdminUserSeeder::adminPassword();
        }

        if (! $this->option('force') && $this->input->isInteractive()) {
            if (! $this->confirm("Create or update admin user {$email}?", true)) {
                $this->warn('Aborted.');

                return self::SUCCESS;
            }
        }

        AdminUserSeeder::syncAdmin($email, $name, $password);
        $this->info("Admin user saved to the database (persistent): {$email}");

        return self::SUCCESS;
    }
}
