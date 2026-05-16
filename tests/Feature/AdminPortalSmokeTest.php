<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * HTTP smoke: every primary admin GET surface after DatabaseSeeder.
 * Ensures blades/controllers load without 500s for the full backend shell.
 */
class AdminPortalSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! User::where('email', 'demo.guest@elapse.test')->exists()) {
            $this->seed(DatabaseSeeder::class);
        }
    }

    public function test_admin_get_routes_return_success(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        $this->actingAs($admin);

        $room = Room::query()->first();
        $this->assertNotNull($room);

        $booking = Booking::query()->first();
        $this->assertNotNull($booking);

        $blog = BlogPost::query()->first();
        $this->assertNotNull($blog);

        $paths = [
            '/admin/dashboard',
            '/admin/dashboard/stats',
            '/booking/list',
            '/edit_booking/'.$booking->id,
            '/view/room/list',
            '/add/room/list',
            '/edit/room/'.$room->id,
            '/room/type/list',
            '/add/room/type',
            '/book/area',
            '/site/setting',
            '/smtp/setting',
            '/all/testimonial',
            '/add/testimonial',
            '/blog/category',
            '/all/blog/post',
            '/add/blog/post',
            '/all/comment/',
            '/booking/report/',
            '/all/gallery',
            '/add/gallery',
            '/contact/message',
            '/all/team',
            '/add/team',
            '/all/permission',
            '/add/permission',
            '/all/roles',
            '/add/roles',
            '/all/admin',
            '/add/admin',
            '/admin/reviews',
            '/admin/loyalty/rewards',
            '/admin/loyalty/rewards/create',
            '/admin/policies',
            '/admin/policies/create',
            '/admin/coupons',
            '/admin/coupons/create',
            '/admin/pricing',
            '/admin/pricing/create',
            '/admin/verification/properties',
            '/admin/verification/hosts',
            '/admin/profile',
            '/admin/change/password',
        ];

        foreach ($paths as $path) {
            $response = $this->get($path);
            $this->assertTrue(
                $response->isSuccessful(),
                "GET {$path} expected 2xx, got {$response->getStatusCode()}"
            );
        }

        $this->get('/edit/blog/post/'.$blog->id)->assertOk();

        $review = Review::query()->first();
        if ($review) {
            $this->get('/admin/reviews/'.$review->id)->assertOk();
        }
    }
}
