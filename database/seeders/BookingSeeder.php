<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Room;
use App\Models\Property;
use App\Models\RoomBookedDate;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create test users
        $user1 = User::where('email', 'user@hotel.com')->first();
        if (!$user1) {
            $user1 = User::create([
                'name' => 'Test User',
                'email' => 'user@hotel.com',
                'password' => bcrypt('111'),
                'role' => 'user',
                'phone' => '1234567890',
            ]);
        }

        // Get properties and rooms
        $properties = Property::with('activeRooms')->get();
        
        if ($properties->isEmpty()) {
            $this->command->warn('No properties found. Please run PropertySeeder first.');
            return;
        }

        $bookingStatuses = [
            ['payment_status' => 1, 'status' => 1, 'payment_method' => 'Razorpay'], // Paid and Complete
            ['payment_status' => 0, 'status' => 0, 'payment_method' => 'Razorpay'], // Pending Payment
            ['payment_status' => 1, 'status' => 1, 'payment_method' => 'Stripe'], // Paid with Stripe
        ];

        $bookingsCreated = 0;

        foreach ($properties as $property) {
            if ($property->activeRooms->isEmpty()) {
                continue;
            }

            $room = $property->activeRooms->first();

            // Create 3-5 bookings per property
            $numBookings = rand(3, 5);

            for ($i = 0; $i < $numBookings; $i++) {
                // Random dates in the future
                $checkIn = Carbon::now()->addDays(rand(7, 60));
                $checkOut = $checkIn->copy()->addDays(rand(1, 7));
                $nights = $checkIn->diffInDays($checkOut);

                // Check if room is already booked for these dates
                $existingBooking = RoomBookedDate::where('room_id', $room->id)
                    ->whereBetween('book_date', [$checkIn->format('Y-m-d'), $checkOut->subDay()->format('Y-m-d')])
                    ->exists();

                if ($existingBooking) {
                    continue; // Skip if dates are already booked
                }

                $numberOfRooms = rand(1, 2);
                $guests = rand(1, 4);
                $subtotal = $room->price * $nights * $numberOfRooms;
                $discount = rand(0, 20); // 0-20% discount
                $discountAmount = ($discount / 100) * $subtotal;
                $totalPrice = $subtotal - $discountAmount;

                $bookingStatus = $bookingStatuses[array_rand($bookingStatuses)];

                $booking = Booking::create([
                    'rooms_id' => $room->id,
                    'property_id' => $property->id,
                    'user_id' => $user1->id,
                    'check_in' => $checkIn->format('Y-m-d'),
                    'check_out' => $checkOut->format('Y-m-d'),
                    'persion' => $guests,
                    'number_of_rooms' => $numberOfRooms,
                    'total_night' => $nights,
                    'actual_price' => $room->price,
                    'subtotal' => $subtotal,
                    'discount' => $discountAmount,
                    'total_price' => $totalPrice,
                    'payment_method' => $bookingStatus['payment_method'],
                    'payment_status' => $bookingStatus['payment_status'],
                    'status' => $bookingStatus['status'],
                    'transation_id' => $bookingStatus['payment_status'] == 1 ? 'txn_' . strtoupper(uniqid()) : '',
                    'name' => $user1->name,
                    'email' => $user1->email,
                    'phone' => $user1->phone ?? '1234567890',
                    'country' => 'India',
                    'state' => 'Test State',
                    'zip_code' => rand(100000, 999999),
                    'address' => 'Test Address ' . rand(1, 100),
                    'code' => rand(100000000, 999999999),
                    'created_at' => Carbon::now()->subDays(rand(0, 30)),
                ]);

                // Create booked dates
                $sdate = $checkIn->format('Y-m-d');
                $edate = $checkOut->format('Y-m-d');
                $eldate = Carbon::create($edate)->subDay();
                $d_period = CarbonPeriod::create($sdate, $eldate);
                
                foreach ($d_period as $period) {
                    RoomBookedDate::create([
                        'booking_id' => $booking->id,
                        'room_id' => $room->id,
                        'book_date' => $period->format('Y-m-d'),
                    ]);
                }

                $bookingsCreated++;
            }
        }

        $this->command->info("Created {$bookingsCreated} test bookings!");
    }
}

