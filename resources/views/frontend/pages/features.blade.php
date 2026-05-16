@extends('frontend.main_master')
@section('main')

<!-- Page Header -->
<section class="page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-4 fw-bold mb-3">{{ __('features_page.hero_title') }}</h1>
                <p class="lead">{{ __('features_page.hero_sub') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Main Features -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="feature-icon mb-3">
                        <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class='bx bx-search-alt-2 text-primary' style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3">{{ __('features_page.search_title') }}</h4>
                    <p class="text-muted">{{ __('features_page.search_p') }}</p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.search_l1') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.search_l2') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.search_l3') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.search_l4') }}</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="feature-icon mb-3">
                        <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class='bx bx-message-dots text-success' style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3">{{ __('features_page.msg_title') }}</h4>
                    <p class="text-muted">{{ __('features_page.msg_p') }}</p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.msg_l1') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.msg_l2') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.msg_l3') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.msg_l4') }}</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="feature-icon mb-3">
                        <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class='bx bx-shield-check text-warning' style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3">{{ __('features_page.verified_title') }}</h4>
                    <p class="text-muted">{{ __('features_page.verified_p') }}</p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.verified_l1') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.verified_l2') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.verified_l3') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.verified_l4') }}</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="feature-icon mb-3">
                        <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class='bx bx-credit-card text-info' style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3">{{ __('features_page.pay_title') }}</h4>
                    <p class="text-muted">{{ __('features_page.pay_p') }}</p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.pay_l1') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.pay_l2') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.pay_l3') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.pay_l4') }}</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="feature-icon mb-3">
                        <div class="icon-wrapper bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class='bx bx-refresh text-danger' style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3">{{ __('features_page.cancel_title') }}</h4>
                    <p class="text-muted">{{ __('features_page.cancel_p') }}</p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.cancel_l1') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.cancel_l2') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.cancel_l3') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.cancel_l4') }}</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="feature-icon mb-3">
                        <div class="icon-wrapper bg-purple bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class='bx bx-star text-purple' style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3">{{ __('features_page.reviews_title') }}</h4>
                    <p class="text-muted">{{ __('features_page.reviews_p') }}</p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.reviews_l1') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.reviews_l2') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.reviews_l3') }}</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('features_page.reviews_l4') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional Features -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">{{ __('features_page.more_title') }}</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <i class='bx bx-wifi text-primary' style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mt-3 mb-2">{{ __('features_page.wifi_title') }}</h5>
                    <p class="text-muted small">{{ __('features_page.wifi_p') }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <i class='bx bx-support text-success' style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mt-3 mb-2">{{ __('features_page.support24_title') }}</h5>
                    <p class="text-muted small">{{ __('features_page.support24_p') }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <i class='bx bx-gift text-warning' style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mt-3 mb-2">{{ __('features_page.loyalty_title') }}</h5>
                    <p class="text-muted small">{{ __('features_page.loyalty_p') }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <i class='bx bx-mobile text-info' style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mt-3 mb-2">{{ __('features_page.mobile_title') }}</h5>
                    <p class="text-muted small">{{ __('features_page.mobile_p') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container text-center text-white">
        <h2 class="display-5 fw-bold mb-3">{{ __('features_page.cta_title') }}</h2>
        <p class="lead mb-4" style="opacity: 0.95;">{{ __('features_page.cta_sub') }}</p>
        <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="btn btn-light btn-lg px-5 rounded-pill fw-bold">
            <i class='bx bx-search-alt-2 me-2'></i>{{ __('features_page.cta_search') }}
        </a>
    </div>
</section>

<style>
.bg-purple {
    background-color: #6f42c1 !important;
}
.text-purple {
    color: #6f42c1 !important;
}
</style>

@endsection
