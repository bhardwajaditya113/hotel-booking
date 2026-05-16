<?php

namespace App\Providers;

use App\Models\SmtpSetting;
use Config;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private static bool $adminUserEnsured = false;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        }

        if (app()->runningInConsole()) {
            return;
        }

        try {
            if (\Schema::hasTable('smtp_settings')) {
                $smtpsetting = SmtpSetting::first();

                if ($smtpsetting) {
                    $data = [
                        'driver' => $smtpsetting->mailer,
                        'host' => $smtpsetting->host,
                        'port' => $smtpsetting->port,
                        'username' => $smtpsetting->username,
                        'password' => $smtpsetting->password,
                        'encryption' => $smtpsetting->encryption,
                        'from' => [
                            'address' => $smtpsetting->from_address,
                            'name' => 'Easyhotel',
                        ],
                    ];
                    Config::set('mail', $data);
                }

            }

            if (config('app.ensure_admin_on_boot') && \Schema::hasTable('users') && ! self::$adminUserEnsured) {
                self::$adminUserEnsured = true;
                AdminUserSeeder::syncAdmin();
            }
        } catch (\Throwable $e) {
            // Skip boot-time DB access when database is unavailable (e.g., build/deploy)
            return;
        }

    }
}
