<?php

namespace Database\Seeders;

/**
 * Curated leisure / hospitality imagery (Unsplash — free to use under Unsplash License).
 * Used by seeders so fresh installs look polished without bundling large binaries.
 */
final class DemoMedia
{
    public const HERO_POOL = 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=2000&q=85';

    public const RESORT_LOBBY = 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1600&q=85';

    public const SUITE_BEDROOM = 'https://images.unsplash.com/photo-1631049307264-da0cfb9d7038?auto=format&fit=crop&w=1600&q=85';

    public const INFINITY_POOL = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1600&q=85';

    public const BEACH_VILLA = 'https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?auto=format&fit=crop&w=1600&q=85';

    public const SPA_LOUNGE = 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1600&q=85';

    public const CITY_HOTEL = 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?auto=format&fit=crop&w=1600&q=85';

    public const ROOFTOP = 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1600&q=85';

    public const BRUNCH = 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=1600&q=85';

    public const LOFT = 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1600&q=85';

    public const BEACH_SUNSET = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=85';

    /**
     * Default site logo path under public/ (not a property photo — avoids hotel imagery in the navbar “logo” slot).
     * Stored in site_settings.logo; resolved via asset().
     */
    public const BRAND_MARK = 'frontend/assets/img/elapse-mark.svg';

    /**
     * @return list<string>
     */
    public static function propertyGallery(int $index): array
    {
        $sets = [
            [self::CITY_HOTEL, self::SUITE_BEDROOM, self::SPA_LOUNGE],
            [self::RESORT_LOBBY, self::SUITE_BEDROOM],
            [self::ROOFTOP, self::INFINITY_POOL],
            [self::BEACH_VILLA, self::INFINITY_POOL, self::BRUNCH],
            [self::LOFT, self::SUITE_BEDROOM],
            [self::HERO_POOL, self::SPA_LOUNGE],
            [self::RESORT_LOBBY, self::ROOFTOP],
            [self::INFINITY_POOL, self::BEACH_SUNSET],
        ];

        return $sets[$index % count($sets)];
    }

    public static function roomImage(int $index): string
    {
        $urls = [self::SUITE_BEDROOM, self::INFINITY_POOL, self::SPA_LOUNGE, self::CITY_HOTEL, self::BEACH_VILLA, self::ROOFTOP];

        return $urls[$index % count($urls)];
    }

    /**
     * @return list<string>
     */
    public static function gallerySet(): array
    {
        return [
            self::HERO_POOL,
            self::INFINITY_POOL,
            self::BEACH_VILLA,
            self::SPA_LOUNGE,
            self::ROOFTOP,
            self::BRUNCH,
        ];
    }

    /**
     * @return list<string>
     */
    public static function blogCovers(): array
    {
        return [
            'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1200&q=85',
            self::SPA_LOUNGE,
            'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=1200&q=85',
            self::BRUNCH,
        ];
    }
}
