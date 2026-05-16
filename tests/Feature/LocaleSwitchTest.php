<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_reflects_session_locale(): void
    {
        $this->withSession(['locale' => 'de'])
            ->get('/')
            ->assertOk()
            ->assertSee('Startseite', false);
    }

    public function test_locale_switch_route_sets_session_and_redirects(): void
    {
        $response = $this->from('/')->get('/locale/de');
        $response->assertRedirect('/');
        $this->assertSame('de', session('locale'));
    }

    public function test_invalid_locale_returns_404(): void
    {
        $this->get('/locale/xx')->assertNotFound();
    }
}
