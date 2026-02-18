<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SiteSetting;
use App\Models\BookArea;

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
                'logo' => 'upload/logo/logo.png',
                'phone' => '+1 (555) 123-4567',
                'address' => '123 Hotel Street, City, Country',
                'email' => 'info@luxuryhotel.com',
                'facebook' => 'https://facebook.com/luxuryhotel',
                'twitter' => 'https://twitter.com/luxuryhotel',
                'copyright' => '© 2026 Luxury Hotel. All Rights Reserved.',
            ]);
            $this->command->info('Site Settings seeded!');
        }

        // Seed Book Area (if not already seeded)
        if (BookArea::count() === 0) {
            BookArea::create([
                'short_title' => 'Book Your Stay',
                'main_title' => 'Experience Luxury Accommodation',
                'short_desc' => 'Discover our premium rooms with world-class amenities. Book now and enjoy exclusive deals on your stay.',
                'link_url' => '/rooms',
                'image' => 'upload/bookarea/default.jpg',
            ]);
            $this->command->info('Book Area seeded!');
        }

        $this->command->info('Site Data seeded successfully!');
    }
}
