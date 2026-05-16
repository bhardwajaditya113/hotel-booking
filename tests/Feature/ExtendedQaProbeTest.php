<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\HostProfile;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Extended QA probes beyond AdminPortalSmokeTest — POST flows and edge routes.
 */
class ExtendedQaProbeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! User::where('email', 'demo.guest@elapse.test')->exists()) {
            $this->seed(DatabaseSeeder::class);
        }
    }

    public function test_guest_full_booking_session_cod(): void
    {
        $guest = User::where('email', 'demo.guest@elapse.test')->firstOrFail();
        $room = Room::where('status', 1)->has('room_numbers')->has('property')->firstOrFail();

        $in = Carbon::now()->addDays(350)->toDateString();
        $out = Carbon::now()->addDays(353)->toDateString();

        $this->actingAs($guest);

        $avail = $this->getJson('/check_room_availability/?room_id='.$room->id.'&check_in='.$in.'&check_out='.$out);
        $avail->assertOk();
        $available = (int) $avail->json('available_room');
        $this->assertGreaterThan(0, $available);

        $this->post('/booking/store/', [
            'check_in' => $in,
            'check_out' => $out,
            'persion' => '2',
            'number_of_rooms' => '1',
            'available_room' => (string) $available,
            'room_id' => (string) $room->id,
        ])->assertRedirect(route('checkout'));

        $this->get(route('checkout'))->assertOk();

        $this->post('/checkout/store', [
            'name' => $guest->name,
            'email' => $guest->email,
            'country' => 'India',
            'phone' => '9876543210',
            'address' => '1 Test Lane',
            'state' => 'Karnataka',
            'zip_code' => '560001',
            'payment_method' => 'COD',
        ])->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'user_id' => $guest->id,
            'rooms_id' => $room->id,
        ]);
    }

    public function test_admin_can_verify_property_and_host(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        $property = Property::where('verification_status', 'pending')->first()
            ?? Property::query()->firstOrFail();

        $this->actingAs($admin)
            ->post('/admin/verification/properties/'.$property->id.'/verify', [
                'status' => 'verified',
                'notes' => 'QA approved',
            ])
            ->assertRedirect();

        $property->refresh();
        $this->assertSame('verified', $property->verification_status);

        $hostProfile = HostProfile::query()->first();
        if ($hostProfile) {
            $this->actingAs($admin)
                ->post('/admin/verification/hosts/'.$hostProfile->id.'/verify', [
                    'status' => 'verified',
                    'notes' => 'QA host ok',
                ])
                ->assertRedirect();
        }
    }

    public function test_host_incoming_bookings_route(): void
    {
        $host = User::where('email', 'demo.host@elapse.test')->firstOrFail();
        $this->actingAs($host)
            ->get('/property/bookings/incoming')
            ->assertOk();
    }

    public function test_unauthenticated_guest_redirects_for_protected_routes(): void
    {
        $this->get('/dashboard')->assertRedirect();
        $this->get('/property/dashboard')->assertRedirect();
        $this->get('/admin/dashboard')->assertRedirect();
    }

    public function test_razorpay_payment_page_requires_valid_booking(): void
    {
        $guest = User::where('email', 'demo.guest@elapse.test')->firstOrFail();
        $this->actingAs($guest)->get('/payment/razorpay/999999')->assertNotFound();
    }
}
