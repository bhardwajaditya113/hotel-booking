@props([
    'lat',
    'lng',
    'height' => 380,
    'zoom' => null,
    'title' => '',
    'mapId' => null,
])
@php
    $mapId = $mapId ?? 'nx-map-' . uniqid();
    $zoom = $zoom ?? config('nexstay.maps.property_zoom', 14);
@endphp
<div
    id="{{ $mapId }}"
    class="nx-leaflet-map border-0 shadow-sm"
    style="height: {{ (int) $height }}px; width: 100%; border-radius: 14px; min-height: 280px; z-index: 1;"
    data-nx-leaflet
    data-lat="{{ $lat }}"
    data-lng="{{ $lng }}"
    data-zoom="{{ (int) $zoom }}"
    data-title="{{ e($title) }}"
    role="region"
    aria-label="Map"
></div>
