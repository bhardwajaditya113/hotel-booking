<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Refresh default seeded copy that still referenced generic “luxury hotel” templates.
     */
    public function up(): void
    {
        $year = (int) date('Y');
        $name = config('app.name', 'Elapse');

        $legacyCopyrights = [
            '© 2025 Luxury Hotel. All Rights Reserved.',
            '© 2026 Luxury Hotel. All Rights Reserved.',
        ];
        foreach ($legacyCopyrights as $old) {
            DB::table('site_settings')->where('copyright', $old)->update([
                'copyright' => '© '.$year.' '.$name.'. All rights reserved.',
            ]);
        }

        DB::table('site_settings')->where('address', '123 Hotel Street, City, Country')->update([
            'address' => '100 Market Street, City, Country',
        ]);

        DB::table('site_settings')->where('email', 'info@luxuryhotel.com')->update([
            'email' => 'hello@elapse.com',
        ]);

        DB::table('site_settings')->where('facebook', 'https://facebook.com/luxuryhotel')->update([
            'facebook' => 'https://facebook.com/elapse',
        ]);

        DB::table('site_settings')->where('twitter', 'https://twitter.com/luxuryhotel')->update([
            'twitter' => 'https://twitter.com/elapse',
        ]);

        DB::table('book_areas')->where('main_title', 'Experience Luxury Accommodation')->update([
            'short_title' => 'Book your next stay',
            'main_title' => 'Stays worth the journey',
            'short_desc' => 'Browse verified places to stay, compare options, and book with clear pricing — built for modern travelers.',
        ]);
    }

    public function down(): void
    {
        // Non-reversible template refresh; no-op.
    }
};
