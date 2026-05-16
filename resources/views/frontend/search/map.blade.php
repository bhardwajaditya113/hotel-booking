@extends('frontend.main_master')

@section('main')
@php
    $markersJson = $markers->toJson(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp

<section class="nx-map-explore border-bottom">
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="h3 fw-bold mb-1">{{ __('frontend.search.map_title') }}</h1>
                <p class="small text-muted mb-0">{{ __('frontend.search.map_subtitle') }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('search.results', array_filter(['search_mode' => 'properties', 'city' => request('city'), 'latitude' => request('latitude'), 'longitude' => request('longitude'), 'radius' => request('radius')])) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class='bx bx-list-ul me-1'></i>{{ __('frontend.search.list_view') }}
                </a>
                <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">{{ __('site.nav.home') }}</a>
            </div>
        </div>
    </div>
</section>

<div class="nx-map-split d-flex flex-column flex-lg-row" style="min-height: calc(100vh - 220px);">
    <aside class="nx-map-sidebar border-end flex-shrink-0 order-2 order-lg-1" style="width:100%; max-width:420px; max-height: 50vh; overflow-y: auto; background: #f8fafc;">
        <div class="p-3 border-bottom bg-white sticky-top">
            <form method="get" action="{{ route('search.map') }}" class="row g-2">
                <input type="hidden" name="search_mode" value="properties">
                <div class="col-12">
                    <x-location-picker
                        :label="__('site.home.where')"
                        :placeholder="__('site.home.where_placeholder')"
                        city-name="city"
                        input-class="form-control"
                    />
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1 rounded-pill">{{ __('frontend.search.update_map') }}</button>
                    <a href="{{ route('search.map') }}" class="btn btn-outline-secondary btn-sm rounded-pill">{{ __('frontend.search.reset') }}</a>
                </div>
            </form>
        </div>
        <div id="nx-map-property-list" class="list-group list-group-flush">
            @forelse($markers as $m)
            <a href="{{ $m['url'] }}" class="list-group-item list-group-item-action nx-map-list-item d-flex gap-3 py-3" data-lat="{{ $m['lat'] }}" data-lng="{{ $m['lng'] }}" data-id="{{ $m['id'] }}">
                <img src="{{ $m['image'] }}" alt="" width="72" height="72" class="rounded-3 object-fit-cover flex-shrink-0" style="object-fit:cover;">
                <div class="min-w-0">
                    <div class="fw-semibold text-dark text-truncate">{{ $m['name'] }}</div>
                    <div class="small text-muted text-truncate">{{ $m['city'] }}</div>
                    @if(!empty($m['min_price']))
                    <div class="small fw-bold text-primary mt-1">{{ __('frontend.search.map_from', ['price' => number_format($m['min_price'])]) }}</div>
                    @endif
                    @if(($m['rating'] ?? 0) > 0)
                    <div class="small text-warning mt-1"><i class='bx bxs-star'></i> {{ number_format($m['rating'], 1) }}</div>
                    @endif
                </div>
            </a>
            @empty
            <div class="p-4 text-center text-muted">
                <p class="mb-2">{{ __('frontend.search.map_empty') }}</p>
                <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="btn btn-primary btn-sm rounded-pill">{{ __('frontend.search.map_browse_all') }}</a>
            </div>
            @endforelse
        </div>
    </aside>
    <div class="nx-map-pane flex-grow-1 order-1 order-lg-2 position-relative" style="min-height: 320px;">
        <div id="nx-search-map" class="w-100 h-100 position-absolute top-0 start-0" style="min-height: 360px;" data-nx-search-map="1"></div>
    </div>
</div>

@push('nexstay-page-scripts')
<script>
window.NEXSTAY_MAP_MARKERS = {!! $markersJson !!};
window.NEXSTAY_MAP_CENTER = @json($center);
window.NEXSTAY_MAP_ZOOM = {{ (int) $mapZoom }};
</script>
<script src="{{ asset('js/nexstay-search-map.js') }}"></script>
@endpush
@endsection
