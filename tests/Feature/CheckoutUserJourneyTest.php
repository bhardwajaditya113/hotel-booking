<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomBookedDate;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Checkout data flow: session → checkout page → place order → confirmation & “my bookings”.
 */
class CheckoutUserJourneyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! User::where('email', 'demo.guest@elapse.test')->exists()) {
            $this->seed(DatabaseSeeder::class);
        }
    }

    public function test_checkout_store_without_session_redirects_to_room_list(): void
    {
        $guest = User::where('email', 'demo.guest@elapse.test')->firstOrFail();
        $this->actingAs($guest)
            ->post('/checkout/store', [
                'name' => 'Test',
                'email' => $guest->email,
                'country' => 'US',
                'phone' => '5550000000',
                'address' => '1 St',
                'state' => 'CA',
                'zip_code' => '90210',
                'payment_method' => 'COD',
            ])
            ->assertRedirect(route('froom.all'));
    }

    public function test_cod_checkout_redirects_to_confirmation_and_lists_in_my_bookings(): void
    {
        $guest = User::where('email', 'demo.guest@elapse.test')->firstOrFail();
        $room = Room::where('status', 1)->has('room_numbers')->firstOrFail();

        $in = Carbon::now()->addDays(340)->toDateString();
        $out = Carbon::now()->addDays(345)->toDateString();
        $availableRoom = (int) $this->actingAs($guest)->getJson(
            '/check_room_availability/?room_id='.$room->id.'&check_in='.$in.'&check_out='.$out
        )->json('available_room');

        $this->assertGreaterThan(0, $availableRoom);

        $this->actingAs($guest)->post('/booking/store/', [
            'check_in' => $in,
            'check_out' => $out,
            'persion' => '2',
            'number_of_rooms' => '1',
            'available_room' => (string) $availableRoom,
            'room_id' => (string) $room->id,
        ])->assertRedirect(route('checkout'));

        $this->actingAs($guest)->get(route('checkout'))->assertOk();

        $response = $this->actingAs($guest)->post('/checkout/store', [
            'name' => $guest->name,
            'email' => $guest->email,
            'country' => 'United Kingdom',
            'phone' => '5551234567',
            'address' => '10 Guest Road',
            'state' => 'London',
            'zip_code' => 'SW1A 1AA',
            'payment_method' => 'COD',
        ]);

        $booking = Booking::where('user_id', $guest->id)->latest('id')->firstOrFail();
        $response->assertRedirect(route('booking.confirmation', ['booking_id' => $booking->id]));

        $this->actingAs($guest)->get(route('booking.confirmation', ['booking_id' => $booking->id]))->assertOk();

        $nights = Carbon::parse($in)->diffInDays(Carbon::parse($out));
        $expectedDates = $nights;
        $this->assertSame(
            $expectedDates,
            RoomBookedDate::where('booking_id', $booking->id)->count(),
            'Each night in [check_in, check_out) should reserve room dates.'
        );

        $this->actingAs($guest)->get('/user/booking')->assertOk();
    }

    public function test_razorpay_test_payment_completes_booking_in_testing(): void
    {
        $this->assertFalse(app()->environment('production'));

        $guest = User::where('email', 'demo.guest@elapse.test')->firstOrFail();
        $room = Room::where('status', 1)->has('room_numbers')->firstOrFail();

        $in = Carbon::now()->addDays(360)->toDateString();
        $out = Carbon::now()->addDays(363)->toDateString();
        $availableRoom = (int) $this->actingAs($guest)->getJson(
            '/check_room_availability/?room_id='.$room->id.'&check_in='.$in.'&check_out='.$out
        )->json('available_room');

        $this->actingAs($guest)->post('/booking/store/', [
            'check_in' => $in,
            'check_out' => $out,
            'persion' => '1',
            'number_of_rooms' => '1',
            'available_room' => (string) $availableRoom,
            'room_id' => (string) $room->id,
        ]);

        $payRedirect = $this->actingAs($guest)->post('/checkout/store', [
            'name' => $guest->name,
            'email' => $guest->email,
            'country' => 'India',
            'phone' => '5558887777',
            'address' => '99 Razorpay Street',
            'state' => 'KA',
            'zip_code' => '560001',
            'payment_method' => 'Razorpay',
        ]);

        $booking = Booking::where('user_id', $guest->id)->latest('id')->firstOrFail();
        $this->assertSame('Razorpay', $booking->payment_method);
        $this->assertSame(0, (int) $booking->payment_status);

        $payRedirect->assertRedirect(route('razorpay.payment', ['booking_id' => $booking->id]));

        $payResponse = $this->actingAs($guest)->post(route('razorpay.test.payment'), [
            'booking_id' => $booking->id,
        ]);

        $booking->refresh();
        $this->assertSame(1, (int) $booking->payment_status);
        $this->assertSame('platform_retained', $booking->marketplace_settlement_status);
        $payResponse->assertRedirect(route('booking.confirmation', ['booking_id' => $booking->id]));
    }
}
