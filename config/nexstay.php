<?php

return [

    'maps' => [
        'default_center' => [
            'lat' => (float) env('NEXSTAY_MAP_DEFAULT_LAT', 20.5937),
            'lng' => (float) env('NEXSTAY_MAP_DEFAULT_LNG', 78.9629),
        ],
        'default_zoom' => (int) env('NEXSTAY_MAP_DEFAULT_ZOOM', 5),
        'property_zoom' => 14,
        'search_default_radius_km' => (int) env('NEXSTAY_SEARCH_RADIUS_KM', 50),
        'tile_url' => env('NEXSTAY_MAP_TILE_URL', 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'),
        'tile_attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        'geocode_provider' => env('NEXSTAY_GEOCODE_PROVIDER', 'photon'),
        'photon_url' => env('NEXSTAY_PHOTON_URL', 'https://photon.komoot.io/api/'),
    ],

];
