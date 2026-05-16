<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SiteDataSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(AdminAccessSeeder::class);
        $this->call(EnhancedFeaturesSeeder::class);
        $this->call(MockDataSeeder::class);
        $this->call(PropertySeeder::class);
        $this->call(PortalDemoSeeder::class);
        $this->call(BookingSeeder::class);
        $this->call(WorkflowDemoSeeder::class);
        $this->call(CheckoutDemoSeeder::class);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
