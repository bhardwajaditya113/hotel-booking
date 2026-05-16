@extends('frontend.main_master')

@section('main')
<div class="inner-banner inner-bg9">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li><a href="{{ url('/') }}">{{ __('site.nav.home') }}</a></li>
                <li><i class="bx bx-chevron-right" aria-hidden="true"></i></li>
                <li aria-current="page">{{ __('frontend.cancellation.policies_title') }}</li>
            </ul>
            <h3>{{ __('frontend.cancellation.policies_title') }}</h3>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <p class="text-muted mb-4">{{ __('frontend.cancellation.intro') }}</p>
            <div class="list-group shadow-sm">
                @forelse($policies as $policy)
                    <div class="list-group-item py-4">
                        <h4 class="h5 mb-2">{{ $policy->name }}</h4>
                        <p class="mb-0 text-secondary">{{ $policy->description }}</p>
                    </div>
                @empty
                    <div class="list-group-item">{{ __('frontend.cancellation.empty') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
