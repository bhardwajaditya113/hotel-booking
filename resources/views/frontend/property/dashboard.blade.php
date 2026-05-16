@extends('frontend.dashboard.account_master')

@section('account_breadcrumb')
    <li><a href="{{ route('dashboard') }}">{{ __('frontend.host_hub.breadcrumb_account') }}</a></li>
@endsection

@section('account_title')
    {{ __('frontend.host_hub.page_title') }}
@endsection

@section('account_content')
<div class="nx-host-hub">
    {{-- Hero --}}
    <div class="nx-host-hub-hero rounded-4 p-4 p-lg-5 mb-4 position-relative overflow-hidden">
        <div class="position-relative" style="z-index: 2;">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
                <div class="flex-grow-1">
                    <p class="text-white-50 small fw-semibold text-uppercase mb-2 mb-lg-3" style="letter-spacing: 0.12em;">{{ __('site.nav.host_hub') }}</p>
                    <h1 class="display-6 fw-bold text-white mb-2 mb-lg-3">{{ __('frontend.host_hub.hero_title') }}</h1>
                    <p class="text-white mb-0 opacity-90" style="max-width: 34rem;">{{ __('frontend.host_hub.hero_sub') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 flex-shrink-0">
                    <a href="{{ route('property.create') }}" class="btn btn-light btn-lg px-4 rounded-pill fw-bold shadow-sm">
                        <i class='bx bx-plus-circle me-2'></i>{{ __('frontend.host_hub.cta_new_listing') }}
                    </a>
                    <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="btn btn-outline-light btn-lg px-4 rounded-pill fw-semibold border-2">
                        {{ __('frontend.host_hub.cta_search_guest') }}
                    </a>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-3 mt-4 pt-lg-2">
                <a href="{{ route('messages.index') }}" class="small text-white text-decoration-none opacity-90 nx-host-quicklink">
                    <i class='bx bx-message-dots me-1'></i>{{ __('frontend.host_hub.quick_messages') }}
                </a>
                <a href="{{ route('property.bookings.incoming') }}" class="small text-white text-decoration-none opacity-90 nx-host-quicklink">
                    <i class='bx bx-calendar-check me-1'></i>{{ __('frontend.host_hub.quick_incoming_bookings') }}
                </a>
            </div>
        </div>
    </div>

    {{-- KPI strip --}}
    @isset($stats)
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-2 col-md-4">
                <div class="nx-host-kpi h-100">
                    <div class="nx-host-kpi-label">{{ __('frontend.host_hub.stat_properties') }}</div>
                    <div class="nx-host-kpi-value">{{ $stats['total_properties'] }}</div>
                    <i class='bx bx-building-house nx-host-kpi-icon'></i>
                </div>
            </div>
            <div class="col-6 col-xl-2 col-md-4">
                <div class="nx-host-kpi h-100 nx-host-kpi--accent">
                    <div class="nx-host-kpi-label">{{ __('frontend.host_hub.stat_active') }}</div>
                    <div class="nx-host-kpi-value">{{ $stats['active_properties'] }}</div>
                    <i class='bx bx-broadcast nx-host-kpi-icon'></i>
                </div>
            </div>
            <div class="col-6 col-xl-2 col-md-4">
                <div class="nx-host-kpi h-100">
                    <div class="nx-host-kpi-label">{{ __('frontend.host_hub.stat_pending') }}</div>
                    <div class="nx-host-kpi-value">{{ $stats['pending_verification'] }}</div>
                    <i class='bx bx-time-five nx-host-kpi-icon'></i>
                </div>
            </div>
            <div class="col-6 col-xl-2 col-md-4">
                <div class="nx-host-kpi h-100 {{ ($stats['pending_booking_requests'] ?? 0) > 0 ? 'nx-host-kpi--accent' : '' }}">
                    <div class="nx-host-kpi-label">{{ __('frontend.host_hub.stat_booking_requests') }}</div>
                    <div class="nx-host-kpi-value">{{ $stats['pending_booking_requests'] ?? 0 }}</div>
                    <i class='bx bx-user-check nx-host-kpi-icon'></i>
                </div>
            </div>
            <div class="col-6 col-xl-2 col-md-4">
                <div class="nx-host-kpi h-100">
                    <div class="nx-host-kpi-label">{{ __('frontend.host_hub.stat_bookings') }}</div>
                    <div class="nx-host-kpi-value">{{ $stats['total_bookings'] }}</div>
                    <i class='bx bx-book-bookmark nx-host-kpi-icon'></i>
                </div>
            </div>
            <div class="col-6 col-xl-2 col-md-4">
                <div class="nx-host-kpi h-100">
                    <div class="nx-host-kpi-label">{{ __('frontend.host_hub.stat_revenue') }}</div>
                    <div class="nx-host-kpi-value nx-host-kpi-value--sm">{{ $stats['total_revenue_display'] }}</div>
                    <i class='bx bx-wallet-alt nx-host-kpi-icon'></i>
                </div>
            </div>
            <div class="col-6 col-xl-2 col-md-4">
                <div class="nx-host-kpi h-100">
                    <div class="nx-host-kpi-label">{{ __('frontend.host_hub.stat_rooms') }}</div>
                    <div class="nx-host-kpi-value">{{ $stats['total_room_units'] }}</div>
                    <i class='bx bx-bed nx-host-kpi-icon'></i>
                </div>
            </div>
        </div>
    @endisset

    {{-- Host profile --}}
    @if ($hostProfile)
        <div class="card border-0 shadow-sm rounded-4 mb-4 nx-host-profile-card">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                    <div>
                        <h2 class="h5 fw-bold mb-3">{{ __('frontend.host_hub.profile_title') }}</h2>
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-3 py-2 border border-secondary border-opacity-25">
                                {{ __('frontend.host_hub.profile_type') }}: {{ ucfirst($hostProfile->type) }}
                            </span>
                            @php
                                $hv = $hostProfile->verification_status;
                                $hvClass = $hv === 'verified' ? 'success' : ($hv === 'rejected' ? 'danger' : 'warning');
                            @endphp
                            <span class="badge rounded-pill bg-{{ $hvClass }} bg-opacity-10 text-{{ $hvClass }} px-3 py-2 border border-{{ $hvClass }} border-opacity-25">
                                {{ __('frontend.host_hub.profile_verification') }}:
                                @if ($hv === 'verified')
                                    {{ __('frontend.host_hub.verify_verified') }}
                                @elseif ($hv === 'rejected')
                                    {{ __('frontend.host_hub.verify_rejected') }}
                                @else
                                    {{ __('frontend.host_hub.verify_pending') }}
                                @endif
                            </span>
                            @if ($hostProfile->is_superhost)
                                <span class="badge rounded-pill text-dark px-3 py-2 nx-host-superhost-badge">
                                    <i class='bx bxs-star'></i> {{ __('frontend.host_hub.profile_superhost') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-md-end">
                        <span class="text-muted small d-block">{{ __('frontend.host_hub.stat_verified') }}</span>
                        <span class="h4 fw-bold text-primary mb-0">{{ $stats['verified_properties'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-primary border-0 rounded-4 mb-4 d-flex align-items-start gap-3 nx-host-alert-soft">
            <i class='bx bx-info-circle fs-3'></i>
            <div>
                <div class="fw-bold">{{ __('frontend.host_hub.profile_incomplete_title') }}</div>
                <p class="mb-2 small">{{ __('frontend.host_hub.profile_incomplete_body') }}</p>
                <a href="{{ route('property.create') }}" class="btn btn-primary btn-sm rounded-pill fw-semibold">{{ __('frontend.host_hub.cta_new_listing') }}</a>
            </div>
        </div>
    @endif

    {{-- Listings --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1">{{ __('frontend.host_hub.listings_title') }}</h2>
            <p class="text-muted small mb-0">{{ __('frontend.host_hub.listings_sub') }}</p>
        </div>
        <a href="{{ route('property.create') }}" class="btn btn-outline-primary rounded-pill fw-semibold align-self-start align-self-md-auto">
            <i class='bx bx-plus me-1'></i>{{ __('frontend.host_hub.cta_new_listing') }}
        </a>
    </div>

    @forelse ($properties as $property)
        @php
            $rooms = $property->activeRooms->count();
            $v = $property->verification_status;
            $vLabel = $v === 'verified' ? __('frontend.host_hub.verify_verified') : ($v === 'rejected' ? __('frontend.host_hub.verify_rejected') : __('frontend.host_hub.verify_pending'));
            $vTone = $v === 'verified' ? 'success' : ($v === 'rejected' ? 'danger' : 'warning');
        @endphp
        <article class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden nx-host-listing-card">
            <div class="row g-0">
                <div class="col-md-4 position-relative">
                    <img src="{{ $property->cover_image_url }}" alt="" class="w-100 nx-host-listing-cover" loading="lazy" width="480" height="320">
                    <span class="position-absolute top-0 start-0 m-3 badge rounded-pill px-3 py-2 fw-semibold shadow-sm nx-host-listing-badge-type">
                        {{ $property->listing_type === 'hotel' ? __('frontend.host_hub.listing_hotel') : __('frontend.host_hub.listing_unique') }}
                    </span>
                </div>
                <div class="col-md-8">
                    <div class="card-body p-4 d-flex flex-column h-100">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                            <div>
                                <h3 class="h5 fw-bold mb-1">{{ $property->name }}</h3>
                                <p class="text-muted small mb-0">
                                    <i class='bx bx-map-pin'></i>
                                    {{ $property->city }}{{ $property->country ? ', '.$property->country : '' }}
                                </p>
                            </div>
                            <span class="badge rounded-pill bg-{{ $property->status === 'active' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $property->status === 'active' ? 'success' : 'secondary' }} border border-{{ $property->status === 'active' ? 'success' : 'secondary' }} border-opacity-25 px-3 py-2">
                                {{ $property->status === 'active' ? __('frontend.host_hub.status_active') : __('frontend.host_hub.status_other') }}
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge rounded-pill bg-light text-dark border px-3 py-2">{{ $property->type->name ?? '—' }}</span>
                            <span class="badge rounded-pill bg-{{ $vTone }} bg-opacity-10 text-{{ $vTone }} border border-{{ $vTone }} border-opacity-25 px-3 py-2">{{ $vLabel }}</span>
                            <span class="badge rounded-pill bg-white text-muted border px-3 py-2">
                                {{ __('frontend.host_hub.rooms_count', ['count' => $rooms]) }}
                            </span>
                        </div>
                        <div class="mt-auto d-flex flex-wrap gap-2 pt-3 border-top border-secondary border-opacity-25">
                            <a href="{{ route('property.show', $property->id) }}" class="btn btn-primary rounded-pill px-4 fw-semibold btn-sm">
                                <i class='bx bx-show me-1'></i>{{ __('frontend.host_hub.action_preview') }}
                            </a>
                            <a href="{{ route('property.edit', $property->id) }}" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold btn-sm">
                                <i class='bx bx-edit-alt me-1'></i>{{ __('frontend.host_hub.action_edit') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    @empty
        <div class="text-center py-5 px-4 rounded-4 border border-dashed nx-host-empty">
            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-4 nx-host-empty-icon">
                <i class='bx bx-building-house text-primary'></i>
            </div>
            <h3 class="h5 fw-bold">{{ __('frontend.host_hub.empty_title') }}</h3>
            <p class="text-muted mx-auto mb-4" style="max-width: 26rem;">{{ __('frontend.host_hub.empty_body') }}</p>
            <a href="{{ route('property.create') }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold">
                {{ __('frontend.host_hub.empty_cta') }}
            </a>
        </div>
    @endforelse
</div>
@endsection
