<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $year = (int) date('Y');
        $name = 'Elapse';

        $legacyCopyrights = [
            '© 2025 Hotel Booking. All Rights Reserved.',
            '© 2026 Hotel Booking. All Rights Reserved.',
            '© '.$year.' Hotel Booking. All rights reserved.',
        ];

        foreach ($legacyCopyrights as $old) {
            DB::table('site_settings')->where('copyright', $old)->update([
                'copyright' => '© '.$year.' '.$name.'. All rights reserved.',
            ]);
        }

        DB::table('site_settings')
            ->where('copyright', 'like', '%Hotel Booking%')
            ->update([
                'copyright' => '© '.$year.' '.$name.'. All rights reserved.',
            ]);
    }

    public function down(): void
    {
        // Non-reversible branding refresh.
    }
};
