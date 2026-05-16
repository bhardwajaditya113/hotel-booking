<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Profile routes use UserController (not default Breeze ProfileController).
 */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated_via_profile_store(): void
    {
        $user = User::factory()->create([
            'address' => 'Old Street',
            'phone' => '111',
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/profile/store', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'address' => 'New Street',
                'phone' => '222',
            ]);

        $response->assertRedirect();

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('New Street', $user->address);
        $this->assertSame('222', $user->phone);
    }
}
