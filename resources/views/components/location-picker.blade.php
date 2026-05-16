@props([
    'cityName' => 'city',
    'latName' => 'latitude',
    'lngName' => 'longitude',
    'radiusName' => 'radius',
    'radiusKm' => null,
    'label' => 'Location',
    'placeholder' => 'City, area, or landmark',
    'inputClass' => 'form-control form-control-lg',
    'labelClass' => 'form-label fw-bold text-dark mb-2',
    'id' => null,
])
@php
    $wrapId = $id ?? 'nx-loc-' . uniqid();
    $radiusKm = $radiusKm ?? config('nexstay.maps.search_default_radius_km', 50);
@endphp
<div id="{{ $wrapId }}" class="nx-location-picker position-relative" data-nx-location-picker="1">
    @if($label !== '')
    <label class="{{ $labelClass }}">{{ $label }}</label>
    @endif
    <input
        type="text"
        name="{{ $cityName }}"
        class="{{ $inputClass }}"
        autocomplete="off"
        placeholder="{{ $placeholder }}"
        value="{{ request($cityName) }}"
        data-nx-city
        aria-autocomplete="list"
        aria-expanded="false"
    >
    <input type="hidden" name="{{ $latName }}" value="{{ request($latName) }}" data-nx-lat>
    <input type="hidden" name="{{ $lngName }}" value="{{ request($lngName) }}" data-nx-lng>
    <input type="hidden" name="{{ $radiusName }}" value="{{ request($radiusName, $radiusKm) }}" data-nx-radius>
    <ul class="nx-location-suggestions list-group position-absolute w-100 shadow border-0 rounded-3 mt-1" role="listbox" hidden style="z-index: 4000; max-height: 260px; overflow-y: auto;"></ul>
</div>
