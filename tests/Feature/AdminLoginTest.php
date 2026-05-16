<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_sign_in_via_admin_login_form(): void
    {
        $this->seed(UsersTableSeeder::class);

        $response = $this->post('/admin/login', [
            'login' => 'admin@gmail.com',
            'password' => '111',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs(User::where('email', 'admin@gmail.com')->first());
    }

    public function test_admin_login_ignores_stale_user_dashboard_intended_url(): void
    {
        $this->seed(UsersTableSeeder::class);

        $response = $this->withSession(['url.intended' => url('/dashboard')])
            ->post('/admin/login', [
                'login' => 'admin@gmail.com',
                'password' => '111',
            ]);

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_non_admin_cannot_use_admin_login_form(): void
    {
        $this->seed(UsersTableSeeder::class);

        $response = $this->from('/admin/login')->post('/admin/login', [
            'login' => 'user@gmail.com',
            'password' => '111',
        ]);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }
}
