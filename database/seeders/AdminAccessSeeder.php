<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class AdminAccessSeeder extends Seeder
{
    /**
     * Grant Spatie permissions used by admin routes (e.g. team.*) to the seeded admin user.
     */
    public function run(): void
    {
        $adminEmail = config('app.admin_email');
        $admin = User::where('email', $adminEmail)->first();
        if (! $admin) {
            $this->command->warn("Admin user not found ({$adminEmail}); skip AdminAccessSeeder.");

            return;
        }

        $table = config('permission.table_names.permissions', 'permissions');
        $hasGroup = Schema::hasColumn($table, 'group_name');

        foreach (['team.all', 'team.add'] as $name) {
            $attrs = ['name' => $name, 'guard_name' => 'web'];
            $extra = $hasGroup ? ['group_name' => 'Team'] : [];
            Permission::query()->firstOrCreate($attrs, array_merge($attrs, $extra));
        }

        $admin->givePermissionTo(['team.all', 'team.add']);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info("Admin permissions synced for {$adminEmail}");
    }
}
