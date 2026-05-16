<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\Room;
use App\Models\SiteSetting;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * End-to-end HTTP journeys: anonymous visitor, new account, booking, wishlist,
 * admin surface + portal sync version (real-time alignment signal for clients).
 */
class FullPlatformJourneyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! User::where('email', 'demo.guest@elapse.test')->exists()) {
            $this->seed(DatabaseSeeder::class);
        }
    }

    public function test_public_visitor_surfaces_search_api_contact_and_compare(): void
    {
        $room = Room::where('status', 1)->has('room_numbers')->firstOrFail();
        $second = Room::where('status', 1)->where('id', '!=', $room->id)->has('room_numbers')->first();

        $this->get('/cancellation-policies')->assertOk();
        $this->get('/room/'.$room->id.'/cancellation-policy')->assertOk();

        $this->get('/room/'.$room->id.'/reviews')->assertOk();
        $this->get('/room/'.$room->id.'/reviews/load')->assertOk();

        $in = Carbon::now()->addDays(310)->toDateString();
        $out = Carbon::now()->addDays(314)->toDateString();

        $this->getJson('/check_room_availability/?room_id='.$room->id.'&check_in='.$in.'&check_out='.$out)
            ->assertOk()
            ->assertJsonStructure(['available_room', 'total_nights']);

        if ($second) {
            $this->get('/rooms/compare?rooms[]='.$room->id.'&rooms[]='.$second->id)->assertOk();
        }

        $this->post('/store/contact', [
            'name' => 'Site Visitor',
            'email' => 'visitor-'.uniqid('', true).'@example.com',
            'phone' => '5550100999',
            'subject' => 'Question',
            'message' => 'Smoke test message from automated suite.',
        ])->assertRedirect();
    }

    public function test_new_user_registers_dashboard_and_portal_sync_json(): void
    {
        $email = 'journey.'.uniqid('', true).'@example.com';

        $this->post('/register', [
            'name' => 'Journey User',
            'email' => $email,
            'password' => 'Password#1',
            'password_confirmation' => 'Password#1',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();

        $user = User::where('email', $email)->firstOrFail();
        $this->assertSame('user', $user->role);

        $this->get('/dashboard')->assertOk();

        $this->getJson('/portal/sync')
            ->assertOk()
            ->assertJsonStructure(['version']);
    }

    public function test_new_user_wishlist_toggle_and_cod_booking_flow(): void
    {
        $room = Room::where('status', 1)->has('room_numbers')->firstOrFail();
        $email = 'booker.'.uniqid('', true).'@example.com';

        $this->post('/register', [
            'name' => 'Booker Test',
            'email' => $email,
            'password' => 'Password#1',
            'password_confirmation' => 'Password#1',
        ]);

        $user = User::where('email', $email)->firstOrFail();

        $this->post('/wishlist/toggle', ['room_id' => $room->id])->assertRedirect();

        $in = Carbon::now()->addDays(320)->toDateString();
        $out = Carbon::now()->addDays(325)->toDateString();

        $availableRoom = (int) $this->getJson(
            '/check_room_availability/?room_id='.$room->id.'&check_in='.$in.'&check_out='.$out
        )->json('available_room');

        $this->assertGreaterThan(0, $availableRoom);

        $this->post('/booking/store/', [
            'check_in' => $in,
            'check_out' => $out,
            'persion' => '2',
            'number_of_rooms' => '1',
            'available_room' => (string) $availableRoom,
            'room_id' => (string) $room->id,
        ])->assertRedirect(route('checkout'));

        $this->get(route('checkout'))->assertOk();

        $response = $this->post('/checkout/store', [
            'name' => 'Booker Test',
            'email' => $email,
            'country' => 'US',
            'phone' => '5550001111',
            'address' => '1 Test Street',
            'state' => 'CA',
            'zip_code' => '90210',
            'payment_method' => 'COD',
        ]);

        $booking = Booking::where('user_id', $user->id)->latest('id')->first();
        $this->assertNotNull($booking);
        $this->assertSame('COD', $booking->payment_method);

        $response->assertRedirect(route('booking.confirmation', ['booking_id' => $booking->id]));
    }

    public function test_admin_site_update_increments_portal_sync_for_another_session_user(): void
    {
        $guest = User::where('email', 'demo.guest@elapse.test')->firstOrFail();
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        $site = SiteSetting::query()->firstOrFail();

        $vBefore = $this->actingAs($guest)->getJson('/portal/sync')->json('version');

        $this->actingAs($admin)->post('/site/update', [
            'id' => $site->id,
            'phone' => '+1 (999) 555-'.random_int(1000, 9999),
            'address' => $site->address,
            'email' => $site->email,
            'facebook' => $site->facebook ?? '',
            'twitter' => $site->twitter ?? '',
            'copyright' => $site->copyright,
        ])->assertRedirect();

        $vAfter = $this->actingAs($guest)->getJson('/portal/sync')->json('version');

        $this->assertGreaterThan(
            $vBefore,
            $vAfter,
            'After admin saves site settings, portal sync version must increase so guest/host UIs can detect updates (Echo + poll).'
        );
    }

    public function test_admin_authenticates_via_dedicated_admin_login_route(): void
    {
        $this->post('/admin/login', [
            'login' => 'admin@gmail.com',
            'password' => '111',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_booking_save_bumps_portal_sync_version(): void
    {
        $guest = User::where('email', 'demo.guest@elapse.test')->firstOrFail();
        $room = Room::where('status', 1)->has('room_numbers')->firstOrFail();

        $v0 = $this->actingAs($guest)->getJson('/portal/sync')->json('version');

        $in = Carbon::now()->addDays(330)->toDateString();
        $out = Carbon::now()->addDays(333)->toDateString();
        $availableRoom = (int) $this->actingAs($guest)->getJson(
            '/check_room_availability/?room_id='.$room->id.'&check_in='.$in.'&check_out='.$out
        )->json('available_room');

        $this->assertGreaterThan(0, $availableRoom);

        $this->actingAs($guest)->post('/booking/store/', [
            'check_in' => $in,
            'check_out' => $out,
            'persion' => '1',
            'number_of_rooms' => '1',
            'available_room' => (string) $availableRoom,
            'room_id' => (string) $room->id,
        ])->assertRedirect(route('checkout'));

        $response = $this->actingAs($guest)->post('/checkout/store', [
            'name' => 'Guest Demo',
            'email' => $guest->email,
            'country' => 'US',
            'phone' => '5551112222',
            'address' => '9 Demo Lane',
            'state' => 'NY',
            'zip_code' => '10001',
            'payment_method' => 'COD',
        ]);

        $booking = Booking::where('user_id', $guest->id)->latest('id')->first();
        $response->assertRedirect(route('booking.confirmation', ['booking_id' => $booking->id]));

        $v1 = $this->actingAs($guest)->getJson('/portal/sync')->json('version');
        $this->assertGreaterThan($v0, $v1);
    }

    public function test_admin_blog_post_update_bumps_portal_sync_for_guest_poll(): void
    {
        $guest = User::where('email', 'demo.guest@elapse.test')->firstOrFail();
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        $post = BlogPost::query()->firstOrFail();

        $v0 = $this->actingAs($guest)->getJson('/portal/sync')->json('version');

        $newTitle = $post->post_titile.' sync-'.substr(uniqid('', true), -6);

        $this->actingAs($admin)->post('/update/blog/post', [
            'id' => $post->id,
            'blogcat_id' => $post->blogcat_id,
            'post_titile' => $newTitle,
            'short_descp' => $post->short_descp,
            'long_descp' => $post->long_descp,
        ])->assertRedirect();

        $post->refresh();
        $this->actingAs($guest)->get('/blog')->assertOk();

        $v1 = $this->actingAs($guest)->getJson('/portal/sync')->json('version');
        $this->assertGreaterThan($v0, $v1);
    }
}
