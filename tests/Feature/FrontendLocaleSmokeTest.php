<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendLocaleSmokeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    public static function publicPageLocaleCases(): array
    {
        return [
            ['/about', 'Über uns'],
            ['/contact', 'Kontakt'],
            ['/gallery', 'Galerie'],
            ['/blog', 'Blog'],
            ['/how-it-works', 'So funktioniert’s'],
            ['/cancellation-policies', 'Stornierungsrichtlinien'],
            ['/features', 'Plattform-Funktionen'],
            ['/pricing', 'Transparente Preise'],
            ['/user-journey', 'Ihre Reise beginnt hier'],
            ['/how-it-works-host', 'Gastgeber werden'],
            ['/search', 'Finden Sie'],
            ['/search/results?search_mode=properties', 'Unterkünfte suchen'],
        ];
    }

    /** @dataProvider publicPageLocaleCases */
    public function test_public_pages_reflect_german_session_locale(string $path, string $expectedFragment): void
    {
        $this->withSession(['locale' => 'de'])
            ->get($path)
            ->assertOk()
            ->assertSee($expectedFragment, false);
    }
}
