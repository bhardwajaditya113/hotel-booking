<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Conversation;
use App\Models\HostProfile;
use App\Models\Message;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Room;
use App\Models\RoomBookedDate;
use App\Models\RoomType;
use App\Models\User;
use App\Models\UserLoyalty;
use App\Models\Wallet;
use App\Models\Wishlist;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Deterministic cross-role QA data: demo host property, demo guest trip extras
 * (wishlist, messages, wallet, loyalty) for manual and automated workflow checks.
 */
class WorkflowDemoSeeder extends Seeder
{
    public function run(): void
    {
        $guest = User::where('email', 'demo.guest@elapse.test')->first();
        $host = User::where('email', 'demo.host@elapse.test')->first();

        if (! $guest || ! $host) {
            $this->command->warn('WorkflowDemoSeeder: run PortalDemoSeeder first (demo.guest / demo.host).');

            return;
        }

        HostProfile::firstOrCreate(
            ['user_id' => $host->id],
            [
                'display_name' => 'Demo Host',
                'phone' => '+919811122233',
                'bio' => 'QA demo host — boutique stays in Bengaluru.',
                'verification_status' => 'verified',
                'is_superhost' => false,
            ]
        );

        $hotelType = PropertyType::where('name', 'Hotel')->first();
        if (! $hotelType) {
            $this->command->warn('WorkflowDemoSeeder: PropertyType "Hotel" missing (run PropertySeeder).');

            return;
        }

        $property = Property::updateOrCreate(
            ['slug' => 'elapse-qa-demo-inn'],
            [
                'name' => 'Elapse QA Demo Inn',
                'listing_type' => 'hotel',
                'property_type_id' => $hotelType->id,
                'user_id' => $host->id,
                'address' => '12 Demo Lane, Koramangala',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'country' => 'India',
                'zipcode' => '560095',
                'latitude' => 12.9352,
                'longitude' => 77.6245,
                'description' => 'Seeded property for QA: host dashboard, bookings, and guest–host messaging.',
                'amenities' => ['wifi', 'parking'],
                'images' => [DemoMedia::SUITE_BEDROOM, DemoMedia::INFINITY_POOL],
                'status' => 'active',
                'verification_status' => 'verified',
                'is_featured' => false,
                'instant_book_enabled' => true,
                'average_rating' => 4.7,
                'cover_image' => DemoMedia::SUITE_BEDROOM,
            ]
        );

        $roomType = RoomType::query()->orderBy('id')->first();
        if (! $roomType) {
            $this->command->warn('WorkflowDemoSeeder: no room types (run MockDataSeeder).');

            return;
        }

        $roomA = Room::firstOrCreate(
            [
                'property_id' => $property->id,
                'short_desc' => 'QA Demo Room A',
            ],
            [
                'roomtype_id' => $roomType->id,
                'total_adult' => 2,
                'total_child' => 0,
                'room_capacity' => 2,
                'price' => 3200,
                'size' => 320,
                'view' => 'Garden View',
                'bed_style' => 'King Bed',
                'discount' => 0,
                'description' => 'Seeded room for workflow tests.',
                'status' => 1,
                'image' => DemoMedia::roomImage(0),
            ]
        );

        $roomB = Room::firstOrCreate(
            [
                'property_id' => $property->id,
                'short_desc' => 'QA Demo Room B',
            ],
            [
                'roomtype_id' => $roomType->id,
                'total_adult' => 2,
                'total_child' => 1,
                'room_capacity' => 3,
                'price' => 4100,
                'size' => 380,
                'view' => 'City View',
                'bed_style' => 'Queen Bed',
                'discount' => 5,
                'description' => 'Second seeded room for availability checks.',
                'status' => 1,
                'image' => DemoMedia::roomImage(1),
            ]
        );

        $wishlist = Wishlist::getOrCreateDefault($guest->id);
        $wishlist->addRoom($roomA->id, 'Weekend in Bangalore', Carbon::now()->addMonths(2)->startOfMonth(), Carbon::now()->addMonths(2)->startOfMonth()->addDays(2));
        $otherRoom = Room::query()
            ->where('property_id', '!=', $property->id)
            ->where(function ($q) {
                $q->where('status', 'Active')->orWhere('status', 1);
            })
            ->orderBy('id')
            ->first();
        if ($otherRoom) {
            $wishlist->addRoom($otherRoom->id, 'Also considering this stay');
        }

        $u1 = min($guest->id, $host->id);
        $u2 = max($guest->id, $host->id);
        $conversation = Conversation::firstOrCreate(
            [
                'user1_id' => $u1,
                'user2_id' => $u2,
                'property_id' => $property->id,
            ],
            [
                'booking_id' => null,
                'last_message_at' => now()->subHour(),
            ]
        );

        if ($conversation->messages()->count() === 0) {
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $guest->id,
                'receiver_id' => $host->id,
                'property_id' => $property->id,
                'booking_id' => null,
                'message' => 'Hi — is early check-in possible at Elapse QA Demo Inn?',
                'is_read' => true,
                'read_at' => now()->subMinutes(30),
            ]);
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $host->id,
                'receiver_id' => $guest->id,
                'property_id' => $property->id,
                'booking_id' => null,
                'message' => 'Hello! We can usually do 1 PM if the room is ready. Looking forward to hosting you.',
                'is_read' => false,
            ]);
            $conversation->update(['last_message_at' => now()]);
        }

        $checkIn = Carbon::now()->addDays(120)->startOfDay();
        $checkOut = $checkIn->copy()->addDays(3)->startOfDay();
        $nights = $checkIn->diffInDays($checkOut);
        $rangeEnd = $checkOut->copy()->subDay();

        $overlap = RoomBookedDate::where('room_id', $roomA->id)
            ->whereBetween('book_date', [$checkIn->format('Y-m-d'), $rangeEnd->format('Y-m-d')])
            ->exists();

        if (! $overlap) {
            $subtotal = $roomA->price * $nights;
            $booking = Booking::create([
                'rooms_id' => $roomA->id,
                'property_id' => $property->id,
                'user_id' => $guest->id,
                'check_in' => $checkIn->format('Y-m-d'),
                'check_out' => $checkOut->format('Y-m-d'),
                'persion' => 2,
                'number_of_rooms' => 1,
                'total_night' => $nights,
                'actual_price' => $roomA->price,
                'subtotal' => $subtotal,
                'discount' => 0,
                'total_price' => $subtotal,
                'payment_method' => 'Razorpay',
                'payment_status' => 1,
                'status' => 1,
                'transation_id' => 'txn_demo_'.strtoupper(uniqid()),
                'name' => $guest->name,
                'email' => $guest->email,
                'phone' => $guest->phone ?? '+919876543210',
                'country' => 'India',
                'state' => 'Karnataka',
                'zip_code' => '560095',
                'address' => 'Guest demo address',
                'code' => (string) random_int(100000000, 999999999),
            ]);

            foreach (CarbonPeriod::create($checkIn->format('Y-m-d'), $rangeEnd->format('Y-m-d')) as $period) {
                RoomBookedDate::create([
                    'booking_id' => $booking->id,
                    'room_id' => $roomA->id,
                    'book_date' => $period->format('Y-m-d'),
                ]);
            }
        }

        if (Schema::hasTable('user_loyalty') && Schema::hasTable('loyalty_tiers')) {
            $loyalty = UserLoyalty::getOrCreate($guest->id);
            if ($loyalty->available_points < 500) {
                $loyalty->update([
                    'total_points' => 850,
                    'available_points' => 850,
                    'lifetime_points' => 850,
                    'total_bookings' => max(1, (int) $loyalty->total_bookings),
                    'total_spent' => max(5000.0, (float) $loyalty->total_spent),
                ]);
            }
        }

        if (Schema::hasTable('wallets')) {
            $wallet = Wallet::getOrCreate($guest->id);
            if ((float) $wallet->balance < 100) {
                $wallet->credit(250.0, 'reward', 'Demo wallet credit for QA workflows', null, null, ['source' => 'WorkflowDemoSeeder']);
            }
        }

        $this->command->info('WorkflowDemoSeeder: demo inn, rooms, wishlist, messages, booking window (+120d), loyalty & wallet primed for demo.guest@elapse.test');
    }
}
