<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * HTTP smoke tests for primary journeys after full DatabaseSeeder
 * (includes WorkflowDemoSeeder mock data for demo guest/host).
 */
class WorkflowRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Other feature tests may have triggered migrate:fresh without seeding; ensure demo data exists.
        if (! User::where('email', 'demo.guest@elapse.test')->exists()) {
            $this->seed(DatabaseSeeder::class);
        }
    }

    public function test_guest_can_browse_public_home_search_and_rooms(): void
    {
        $this->get('/')->assertOk();
        $this->get('/search')->assertOk();
        $this->get('/rooms/')->assertOk();
        $this->get('/blog')->assertOk();
        $this->get('/search/map')->assertOk();
    }

    public function test_guest_is_redirected_from_checkout_without_auth(): void
    {
        $this->get('/checkout')->assertRedirect();
    }

    public function test_authenticated_guest_can_open_dashboard_bookings_wishlists_messages_wallet_and_loyalty(): void
    {
        $guest = User::where('email', 'demo.guest@elapse.test')->firstOrFail();

        $this->actingAs($guest);

        $this->get('/dashboard')->assertOk();
        $this->get('/user/booking')->assertOk();
        $this->get('/wishlists')->assertOk();
        $this->get('/messages')->assertOk();
        $this->get('/wallet')->assertOk();
        $this->get('/loyalty')->assertOk();
    }

    public function test_host_can_open_property_dashboard_and_seeded_property(): void
    {
        $host = User::where('email', 'demo.host@elapse.test')->firstOrFail();
        $property = Property::where('slug', 'elapse-qa-demo-inn')->firstOrFail();

        $this->actingAs($host);

        $this->get('/property/dashboard')->assertOk();
        $this->get('/property/'.$property->id)->assertOk();
        $this->get('/property/'.$property->id.'/edit')->assertOk();
    }

    public function test_host_can_open_incoming_reservations(): void
    {
        $host = User::where('email', 'demo.host@elapse.test')->firstOrFail();

        $this->actingAs($host);

        $this->get(route('property.bookings.incoming'))->assertOk();
    }

    public function test_host_can_open_property_create_and_submit_new_listing(): void
    {
        $host = User::where('email', 'demo.host@elapse.test')->firstOrFail();
        $type = PropertyType::query()->orderBy('id')->firstOrFail();

        $this->actingAs($host);

        $this->get(route('property.create'))
            ->assertOk()
            ->assertSee(__('frontend.host_listing.hero_title'), false);

        $name = 'QA Listing '.uniqid('', true);

        $response = $this->post(route('property.store'), [
            'name' => $name,
            'property_type_id' => $type->id,
            'listing_type' => 'unique_stay',
            'address' => '123 Test Street',
            'city' => 'Mumbai',
            'country' => 'India',
            'state' => 'Maharashtra',
            'instant_book_enabled' => '1',
            'cancellation_preset' => 'moderate',
            'check_in_time' => '15:00',
            'check_out_time' => '10:00',
            'description' => 'Automated workflow listing.',
        ]);

        $response->assertRedirect(route('property.dashboard'));

        $this->assertDatabaseHas('properties', [
            'name' => $name,
            'user_id' => $host->id,
            'listing_type' => 'unique_stay',
            'city' => 'Mumbai',
        ]);

        $slug = Property::where('name', $name)->value('slug');
        $this->assertNotEmpty($slug);
    }

    public function test_host_can_update_property_extended_fields(): void
    {
        $host = User::where('email', 'demo.host@elapse.test')->firstOrFail();
        $property = Property::where('slug', 'elapse-qa-demo-inn')->firstOrFail();

        $this->actingAs($host);

        $payload = [
            'name' => $property->name,
            'property_type_id' => $property->property_type_id,
            'address' => $property->address,
            'city' => $property->city,
            'country' => $property->country ?: 'India',
            'state' => $property->state,
            'zipcode' => $property->zipcode,
            'description' => $property->description,
            'phone' => $property->phone,
            'email' => $property->email,
            'latitude' => $property->latitude,
            'longitude' => $property->longitude,
            'instant_book_enabled' => '1',
            'check_in_time' => '15:30',
            'check_out_time' => '10:30',
            'house_rules' => 'Quiet hours after 22:00.',
            'cancellation_preset' => 'moderate',
            'meta_title' => $property->meta_title,
            'meta_description' => $property->meta_description,
        ];

        $response = $this->put(route('property.update', $property->id), $payload);

        $response->assertRedirect(route('property.dashboard'));

        $property->refresh();

        $this->assertSame('Quiet hours after 22:00.', $property->house_rules);
        $this->assertStringStartsWith('15:30', (string) $property->check_in_time);
        $this->assertStringStartsWith('10:30', (string) $property->check_out_time);
    }

    public function test_admin_can_open_admin_dashboard_and_verification_indexes(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();

        $this->actingAs($admin);

        $this->get('/admin/dashboard')->assertOk();
        $this->get('/admin/verification/properties')->assertOk();
        $this->get('/admin/verification/hosts')->assertOk();
    }
}
