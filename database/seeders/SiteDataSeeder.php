<?php

namespace Database\Seeders;

use App\Models\BookArea;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Site Settings
        if (SiteSetting::count() === 0) {
            SiteSetting::create([
                'logo' => DemoMedia::BRAND_MARK,
                'phone' => '+1 (555) 123-4567',
                'address' => '100 Market Street, City, Country',
                'email' => 'hello@elapse.com',
                'facebook' => 'https://facebook.com/elapse',
                'twitter' => 'https://twitter.com/elapse',
                'copyright' => '© '.date('Y').' '.config('app.name', 'Elapse').'. All rights reserved.',
            ]);
            $this->command->info('Site Settings seeded!');
        }

        // Seed Book Area (if not already seeded)
        if (BookArea::count() === 0) {
            BookArea::create([
                'short_title' => 'Book your next stay',
                'main_title' => 'Stays worth the journey',
                'short_desc' => 'Browse verified places to stay, compare options, and book with clear pricing — built for modern travelers.',
                'link_url' => '/rooms',
                'image' => DemoMedia::HERO_POOL,
            ]);
            $this->command->info('Book Area seeded!');
        }

        $this->command->info('Site Data seeded successfully!');
    }
}
