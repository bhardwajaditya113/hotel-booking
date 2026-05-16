@extends('frontend.dashboard.account_master')

@section('account_breadcrumb')
    <li>
        <a href="{{ route('dashboard') }}">{{ __('frontend.host_listing.breadcrumb_account') }}</a>
        <span class="opacity-50 mx-1">/</span>
        <a href="{{ route('property.dashboard') }}">{{ __('frontend.host_listing.breadcrumb_hub') }}</a>
        <span class="opacity-50 mx-1">/</span>
        <span class="text-muted">{{ __('frontend.host_listing.edit_page_title') }}</span>
    </li>
@endsection

@section('account_title')
    {{ __('frontend.host_listing.edit_page_title') }}
@endsection

@section('account_content')
<div class="nx-host-listing-form">
    <div class="nx-host-listing-hero rounded-4 p-4 p-lg-5 mb-4 position-relative overflow-hidden">
        <div class="position-relative" style="z-index: 2;">
            <div class="d-flex flex-column flex-lg-row align-items-lg-start justify-content-between gap-4">
                <div class="flex-grow-1">
                    <p class="text-white-50 small fw-semibold text-uppercase mb-2" style="letter-spacing: 0.12em;">
                        {{ __('frontend.host_listing.edit_hero_kicker') }}
                    </p>
                    <h2 class="h3 fw-bold text-white mb-2">{{ __('frontend.host_listing.edit_hero_title') }}</h2>
                    <p class="text-white mb-0 opacity-90" style="max-width: 38rem;">{{ $property->name }}</p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('property.dashboard') }}" class="btn btn-outline-light rounded-pill px-4 fw-semibold border-2">
                        <i class="bx bx-arrow-back me-1"></i>{{ __('frontend.host_listing.back_dashboard') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if ($types->isEmpty())
        <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4 d-flex gap-3 align-items-start">
            <i class="bx bx-error-circle fs-4 mt-1"></i>
            <div>{{ __('frontend.host_listing.types_missing') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <div class="fw-semibold mb-2">{{ __('frontend.host_listing.validation_heading') }}</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('property.update', $property->id) }}" method="POST" id="nxListingEditForm" class="nx-host-listing-form-inner">
        @csrf
        @method('PUT')

        @include('frontend.property.partials.listing-form-fields', [
            'listing' => $property,
            'types' => $types,
            'countries' => $countries,
            'indianStatesUt' => $indianStatesUt,
            'amenityCategories' => $amenityCategories,
            'submitLabel' => __('frontend.host_listing.submit_update'),
            'submitDisabled' => $types->isEmpty(),
        ])
    </form>
</div>
@endsection

@push('scripts')
    @include('frontend.property.partials.listing-form-scripts')
@endpush
