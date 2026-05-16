<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\Conversation;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use App\Models\Wishlist;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Broad HTTP smoke coverage after full DatabaseSeeder (guest, host, admin).
 * Catches 500s from broken queries, missing views, or bad route bindings.
 */
class EndToEndWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! User::where('email', 'demo.guest@elapse.test')->exists()) {
            $this->seed(DatabaseSeeder::class);
        }
    }

    public function test_public_and_marketing_routes_return_200(): void
    {
        $urls = [
            '/',
            '/about',
            '/rooms/',
            '/blog',
            '/gallery',
            '/contact',
            '/how-it-works',
            '/how-it-works-host',
            '/features',
            '/pricing',
            '/user-journey',
            '/cancellation-policies',
            '/search',
            '/search/map',
            '/search/results',
            '/search/results?search_mode=properties',
            '/properties',
        ];

        foreach ($urls as $url) {
            $response = $this->get($url);
            if ($url === '/properties') {
                $response->assertRedirect();

                continue;
            }
            $response->assertOk();
        }

        $post = BlogPost::query()->first();
        if ($post) {
            $this->get('/blog/details/'.$post->post_slug)->assertOk();
        }

        $room = Room::query()->whereNotNull('property_id')->first()
            ?? Room::query()->first();
        $this->assertNotNull($room);
        $this->get('/room/details/'.$room->id)->assertOk();
        $this->get('/room/'.$room->id.'/reviews')->assertOk();
    }

    public function test_search_with_check_in_check_out_does_not_500(): void
    {
        $in = Carbon::now()->addDays(200)->toDateString();
        $out = Carbon::now()->addDays(205)->toDateString();

        $this->get('/search/results?check_in='.$in.'&check_out='.$out)->assertOk();
        $this->get('/search/results?search_mode=properties&check_in='.$in.'&check_out='.$out)->assertOk();
        $this->get('/search/filter-counts?check_in='.$in.'&check_out='.$out)->assertOk();
    }

    public function test_guest_can_login_via_http_and_open_account_routes(): void
    {
        $guest = User::where('email', 'demo.guest@elapse.test')->firstOrFail();

        $this->post('/login', [
            'login' => $guest->email,
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $routes = [
            '/dashboard',
            '/profile',
            '/user/booking',
            '/wishlists',
            '/messages',
            '/wallet',
            '/wallet/add-money',
            '/loyalty',
            '/loyalty/tiers',
            '/loyalty/history',
            '/loyalty/rewards',
            '/notifications',
            '/notifications/settings',
            '/property/create',
            '/coupon/available',
        ];

        foreach ($routes as $path) {
            $this->get($path)->assertOk();
        }

        $wishlist = Wishlist::where('user_id', $guest->id)->first();
        if ($wishlist) {
            $this->get('/wishlists/'.$wishlist->id)->assertOk();
        }

        $conversation = Conversation::query()
            ->where(function ($q) use ($guest) {
                $q->where('user1_id', $guest->id)->orWhere('user2_id', $guest->id);
            })
            ->first();
        if ($conversation) {
            $this->get('/messages/'.$conversation->id)->assertOk();
        }

        $booking = Booking::where('user_id', $guest->id)
            ->where('status', 1)
            ->whereDate('check_in', '>', now())
            ->orderBy('check_in')
            ->first();
        if ($booking) {
            $this->get('/booking/'.$booking->id.'/cancel')->assertOk();
        }
    }

    public function test_host_can_login_and_open_property_portal(): void
    {
        $host = User::where('email', 'demo.host@elapse.test')->firstOrFail();
        $property = Property::where('slug', 'elapse-qa-demo-inn')->firstOrFail();

        $this->post('/login', [
            'login' => $host->email,
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        foreach ([
            '/property/dashboard',
            '/property/'.$property->id,
            '/property/'.$property->id.'/edit',
            '/property/'.$property->id.'/view',
        ] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_admin_can_login_and_open_backend_routes(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();

        $this->post('/login', [
            'login' => $admin->email,
            'password' => '111',
        ])->assertRedirect(route('admin.dashboard'));

        $paths = [
            '/admin/dashboard',
            '/booking/list',
            '/view/room/list',
            '/admin/reviews',
            '/admin/loyalty/rewards',
            '/admin/policies',
            '/admin/coupons',
            '/admin/pricing',
            '/admin/verification/properties',
            '/admin/verification/hosts',
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertOk();
        }
    }
}
