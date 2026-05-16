<?php

namespace Database\Seeders;

use App\Models\HostProfile;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Room;
use App\Models\RoomNumber;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Bookable listings for payment / checkout QA (Razorpay or COD).
 *
 * php artisan db:seed --class=CheckoutDemoSeeder
 */
class CheckoutDemoSeeder extends Seeder
{
    public const SLUG_MUMBAI = 'checkout-demo-mumbai-marina';

    public const SLUG_GOA = 'checkout-demo-goa-bay';

    public function run(): void
    {
        $this->call(PortalDemoSeeder::class);

        $host = User::where('email', 'demo.host@elapse.test')->first();
        if (! $host) {
            $this->command->warn('CheckoutDemoSeeder: demo.host@elapse.test missing.');

            return;
        }

        HostProfile::updateOrCreate(
            ['user_id' => $host->id],
            [
                'display_name' => 'Demo Host',
                'phone' => '+919811122233',
                'verification_status' => 'verified',
                'is_superhost' => true,
            ]
        );

        $hotelType = PropertyType::firstOrCreate(['name' => 'Hotel']);
        $villaType = PropertyType::firstOrCreate(['name' => 'Villa']);
        $roomType = RoomType::query()->orderBy('id')->first()
            ?? RoomType::firstOrCreate(['name' => 'Deluxe Room']);

        $listings = [
            [
                'slug' => self::SLUG_MUMBAI,
                'name' => 'Checkout Demo — Marina Mumbai',
                'listing_type' => 'hotel',
                'property_type_id' => $hotelType->id,
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'address' => '88 Marine Drive',
                'zipcode' => '400002',
                'latitude' => 18.9432,
                'longitude' => 72.8236,
                'price' => 2499,
                'room_label' => 'Marina Deluxe',
                'gallery' => [DemoMedia::CITY_HOTEL, DemoMedia::SUITE_BEDROOM],
            ],
            [
                'slug' => self::SLUG_GOA,
                'name' => 'Checkout Demo — Goa Bay Villa',
                'listing_type' => 'unique_stay',
                'property_type_id' => $villaType->id,
                'city' => 'Goa',
                'state' => 'Goa',
                'address' => '14 Calangute Beach Road',
                'zipcode' => '403516',
                'latitude' => 15.5439,
                'longitude' => 73.7553,
                'price' => 1899,
                'room_label' => 'Bay View Suite',
                'gallery' => [DemoMedia::BEACH_VILLA, DemoMedia::INFINITY_POOL],
            ],
        ];

        foreach ($listings as $idx => $row) {
            $property = Property::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'listing_type' => $row['listing_type'],
                    'property_type_id' => $row['property_type_id'],
                    'user_id' => $host->id,
                    'address' => $row['address'],
                    'city' => $row['city'],
                    'state' => $row['state'],
                    'country' => 'India',
                    'zipcode' => $row['zipcode'],
                    'latitude' => $row['latitude'],
                    'longitude' => $row['longitude'],
                    'description' => 'Seeded for payment checkout QA. Instant book, verified, with inventory.',
                    'amenities' => ['wifi', 'parking', 'breakfast'],
                    'images' => $row['gallery'],
                    'cover_image' => $row['gallery'][0],
                    'status' => 'active',
                    'verification_status' => 'verified',
                    'is_featured' => true,
                    'instant_book_enabled' => true,
                    'average_rating' => 4.8,
                    'check_in_time' => '14:00:00',
                    'check_out_time' => '11:00:00',
                ]
            );

            $room = Room::updateOrCreate(
                [
                    'property_id' => $property->id,
                    'short_desc' => $row['room_label'],
                ],
                [
                    'roomtype_id' => $roomType->id,
                    'total_adult' => 2,
                    'total_child' => 1,
                    'room_capacity' => 3,
                    'price' => $row['price'],
                    'size' => 360,
                    'view' => 'Sea View',
                    'bed_style' => 'King Bed',
                    'discount' => 0,
                    'description' => 'Bookable room for Razorpay / COD checkout tests.',
                    'status' => 1,
                    'image' => DemoMedia::roomImage($idx),
                ]
            );

            $this->ensureRoomNumbers($room, 8);

            $this->command->info("Checkout listing: {$property->name} (property #{$property->id}, room #{$room->id}, ₹{$row['price']}/night)");
        }

        $qaInn = Property::where('slug', 'elapse-qa-demo-inn')->first();
        if ($qaInn) {
            $qaInn->update([
                'verification_status' => 'verified',
                'instant_book_enabled' => true,
                'is_featured' => true,
                'status' => 'active',
            ]);
            foreach ($qaInn->rooms as $room) {
                $this->ensureRoomNumbers($room, 8);
            }
            $this->command->info("Updated Elapse QA Demo Inn (property #{$qaInn->id}) for checkout.");
        }

        $this->backfillRoomNumbersForVerifiedProperties();

        $this->command->info('');
        $this->command->info('Guest login: demo.guest@elapse.test / password');
        $this->command->info('Try: /search/results?search_mode=properties&city=Mumbai');
        $this->command->info('Or property: /property/{id} — pick dates → checkout → Razorpay');
    }

    private function ensureRoomNumbers(Room $room, int $count = 5): void
    {
        if ($room->room_numbers()->count() >= $count) {
            return;
        }

        $base = 500 + ($room->id * 10);
        for ($i = 1; $i <= $count; $i++) {
            RoomNumber::firstOrCreate(
                [
                    'rooms_id' => $room->id,
                    'room_no' => $base + $i,
                ],
                [
                    'room_type_id' => $room->roomtype_id,
                    'status' => 'Active',
                ]
            );
        }
    }

    private function backfillRoomNumbersForVerifiedProperties(): void
    {
        $rooms = Room::query()
            ->whereHas('property', fn ($q) => $q->active()->verified())
            ->whereDoesntHave('room_numbers')
            ->get();

        foreach ($rooms as $room) {
            $this->ensureRoomNumbers($room, 5);
        }

        if ($rooms->isNotEmpty()) {
            $this->command->info("Backfilled room numbers for {$rooms->count()} room(s) on verified properties.");
        }
    }
}
