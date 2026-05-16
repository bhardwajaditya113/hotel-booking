@extends('frontend.main_master')
@section('main')

<!-- Page Header -->
<section class="page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-4 fw-bold mb-3">{{ __('frontend.pages.how_it_works.hero_title') }}</h1>
                <p class="lead">{{ __('frontend.pages.how_it_works.hero_lead') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- For Guests Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">{{ __('frontend.pages.how_it_works.guests_title') }}</h2>
            <p class="lead text-muted">{{ __('frontend.pages.how_it_works.guests_lead') }}</p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-4">
                <div class="step-card text-center p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-4" style="width: 80px; height: 80px; font-size: 2rem;">
                        1
                    </div>
                    <div class="step-icon mb-4">
                        <i class='bx bx-search-alt-2 text-primary' style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="fw-bold mb-3">{{ __('frontend.pages.how_it_works.step1_title') }}</h3>
                    <p class="text-muted">{{ __('frontend.pages.how_it_works.step1_body') }}</p>
                    <ul class="list-unstyled text-start mt-4">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('frontend.pages.how_it_works.step1_li1') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('frontend.pages.how_it_works.step1_li2') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('frontend.pages.how_it_works.step1_li3') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('frontend.pages.how_it_works.step1_li4') }}</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="step-card text-center p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="step-number bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-4" style="width: 80px; height: 80px; font-size: 2rem;">
                        2
                    </div>
                    <div class="step-icon mb-4">
                        <i class='bx bx-calendar-check text-success' style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="fw-bold mb-3">{{ __('frontend.pages.how_it_works.step2_title') }}</h3>
                    <p class="text-muted">{{ __('frontend.pages.how_it_works.step2_body') }}</p>
                    <ul class="list-unstyled text-start mt-4">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('frontend.pages.how_it_works.step2_li1') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('frontend.pages.how_it_works.step2_li2') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('frontend.pages.how_it_works.step2_li3') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('frontend.pages.how_it_works.step2_li4') }}</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="step-card text-center p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="step-number bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-4" style="width: 80px; height: 80px; font-size: 2rem;">
                        3
                    </div>
                    <div class="step-icon mb-4">
                        <i class='bx bx-happy text-warning' style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="fw-bold mb-3">{{ __('frontend.pages.how_it_works.step3_title') }}</h3>
                    <p class="text-muted">{{ __('frontend.pages.how_it_works.step3_body') }}</p>
                    <ul class="list-unstyled text-start mt-4">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('frontend.pages.how_it_works.step3_li1') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('frontend.pages.how_it_works.step3_li2') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('frontend.pages.how_it_works.step3_li3') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('frontend.pages.how_it_works.step3_li4') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="btn btn-primary btn-lg px-5 rounded-pill fw-bold">
                {{ __('frontend.pages.how_it_works.start_searching') }} <i class='bx bx-arrow-right ms-2'></i>
            </a>
        </div>
    </div>
</section>

<!-- For Hosts Section -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">{{ __('frontend.pages.how_it_works.hosts_title') }}</h2>
            <p class="lead text-muted">{{ __('frontend.pages.how_it_works.hosts_lead') }}</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="host-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-edit text-primary' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">{{ __('frontend.pages.how_it_works.host_create') }}</h5>
                    <p class="text-muted small">{{ __('frontend.pages.how_it_works.host_create_body') }}</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="host-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-check-circle text-success' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">{{ __('frontend.pages.how_it_works.host_verified') }}</h5>
                    <p class="text-muted small">{{ __('frontend.pages.how_it_works.host_verified_body') }}</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="host-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-calendar text-warning' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">{{ __('frontend.pages.how_it_works.host_bookings') }}</h5>
                    <p class="text-muted small">{{ __('frontend.pages.how_it_works.host_bookings_body') }}</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="host-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-money text-info' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">{{ __('frontend.pages.how_it_works.host_earn') }}</h5>
                    <p class="text-muted small">{{ __('frontend.pages.how_it_works.host_earn_body') }}</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('property.create') }}" class="btn btn-primary btn-lg px-5 rounded-pill fw-bold me-3">
                {{ __('frontend.pages.how_it_works.list_property') }} <i class='bx bx-plus-circle ms-2'></i>
            </a>
            <a href="{{ route('how-it-works-host') }}" class="btn btn-outline-primary btn-lg px-5 rounded-pill fw-bold">
                {{ __('frontend.pages.how_it_works.learn_more') }} <i class='bx bx-info-circle ms-2'></i>
            </a>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="display-5 fw-bold mb-4">{{ __('frontend.pages.how_it_works.benefits_title') }}</h2>
                <div class="benefit-item d-flex gap-3 mb-4">
                    <div class="icon-box bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; flex-shrink: 0;">
                        <i class='bx bx-shield-check text-primary' style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-2">{{ __('frontend.pages.how_it_works.benefit_verified_title') }}</h5>
                        <p class="text-muted mb-0">{{ __('frontend.pages.how_it_works.benefit_verified_body') }}</p>
                    </div>
                </div>
                <div class="benefit-item d-flex gap-3 mb-4">
                    <div class="icon-box bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; flex-shrink: 0;">
                        <i class='bx bx-message-dots text-success' style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-2">{{ __('frontend.pages.how_it_works.benefit_comm_title') }}</h5>
                        <p class="text-muted mb-0">{{ __('frontend.pages.how_it_works.benefit_comm_body') }}</p>
                    </div>
                </div>
                <div class="benefit-item d-flex gap-3 mb-4">
                    <div class="icon-box bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; flex-shrink: 0;">
                        <i class='bx bx-refresh text-warning' style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-2">{{ __('frontend.pages.how_it_works.benefit_cancel_title') }}</h5>
                        <p class="text-muted mb-0">{{ __('frontend.pages.how_it_works.benefit_cancel_body') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="image-wrapper position-relative">
                    <img src="{{ asset('frontend/assets/img/room/room-img1.jpg') }}" alt="{{ __('frontend.pages.how_it_works.benefits_img_alt') }}" class="img-fluid rounded-4 shadow-lg">
                    <div class="stats-card position-absolute bg-white rounded-4 p-4 shadow-lg" style="bottom: -20px; right: -20px; max-width: 250px;">
                        <h3 class="display-6 fw-bold text-primary mb-2">4.8/5</h3>
                        <p class="mb-0 text-muted">{{ __('frontend.pages.how_it_works.avg_rating') }}</p>
                        <hr class="my-2">
                        <p class="mb-0 small text-muted"><i class='bx bx-star text-warning'></i> {{ __('frontend.pages.how_it_works.reviews_note') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container text-center text-white">
        <h2 class="display-5 fw-bold mb-3">{{ __('frontend.pages.how_it_works.cta_title') }}</h2>
        <p class="lead mb-4" style="opacity: 0.95;">{{ __('frontend.pages.how_it_works.cta_lead') }}</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="btn btn-light btn-lg px-5 rounded-pill fw-bold">
                <i class='bx bx-search-alt-2 me-2'></i>{{ __('frontend.pages.how_it_works.cta_search') }}
            </a>
            <a href="{{ route('property.create') }}" class="btn btn-outline-light btn-lg px-5 rounded-pill fw-bold">
                <i class='bx bx-plus-circle me-2'></i>{{ __('frontend.pages.how_it_works.cta_host') }}
            </a>
        </div>
    </div>
</section>

@endsection
