<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\HostProfile;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Inserts one repeatable demo listing for QA / screenshots (same slug each run).
 *
 * php artisan db:seed --class=MockListingSeeder
 */
class MockListingSeeder extends Seeder
{
    public const MOCK_SLUG = 'mock-coastal-retreat-elapse-demo';

    public function run(): void
    {
        $host = User::where('email', 'demo.host@elapse.test')->first()
            ?? User::query()->orderBy('id')->first();

        if (! $host) {
            $this->command?->warn('MockListingSeeder: no users in database — run php artisan db:seed first.');

            return;
        }

        $type = PropertyType::query()->orderBy('id')->first()
            ?? PropertyType::create(['name' => 'Hotel']);

        $amenityIds = Amenity::query()->active()->orderBy('id')->limit(10)->pluck('id')->all();

        $name = 'Mock Coastal Retreat (seeded)';
        $presetKey = 'moderate';
        $cancellationBody = __("frontend.host_listing.cancellation_presets.{$presetKey}");
        $description = 'Seeded mock listing with coastal-inspired copy for QA and demos. Wi‑Fi, calm interiors, and walkable neighborhood — refine rooms and photos after verification.';

        $plainDesc = strip_tags($description);

        HostProfile::firstOrCreate(
            ['user_id' => $host->id],
            [
                'display_name' => $host->name,
                'type' => 'individual',
                'verification_status' => 'pending',
            ]
        );

        Property::updateOrCreate(
            ['slug' => self::MOCK_SLUG],
            [
                'name' => $name,
                'property_type_id' => $type->id,
                'listing_type' => 'unique_stay',
                'user_id' => $host->id,
                'address' => '221 Marina Walk',
                'city' => 'Panaji',
                'state' => 'Goa',
                'country' => 'India',
                'zipcode' => '403001',
                'description' => $description,
                'phone' => '+91 98765 43210',
                'email' => 'mock.host.listing@elapse.test',
                'latitude' => '15.49899',
                'longitude' => '73.82825',
                'instant_book_enabled' => true,
                'check_in_time' => '14:00:00',
                'check_out_time' => '11:00:00',
                'amenities' => count($amenityIds) ? $amenityIds : null,
                'house_rules' => 'No parties. Quiet hours 22:00–08:00. No smoking indoors.',
                'cancellation_policy_text' => $cancellationBody,
                'meta_title' => Str::limit($name, 60),
                'meta_description' => Str::limit($plainDesc, 155),
                'status' => 'active',
                'verification_status' => 'pending',
            ]
        );

        $this->command?->info('Mock listing saved: slug '.self::MOCK_SLUG.' (host user id '.$host->id.').');
    }
}
