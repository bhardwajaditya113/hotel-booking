@extends('frontend.main_master')
@section('main')

<!-- Page Header -->
<section class="page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-4 fw-bold mb-3">{{ __('user_journey_page.hero_title') }}</h1>
                <p class="lead">{{ __('user_journey_page.hero_sub') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Guest Journey -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">{{ __('user_journey_page.guest_title') }}</h2>
            <p class="lead text-muted">{{ __('user_journey_page.guest_sub') }}</p>
        </div>
        
        <div class="journey-timeline">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="journey-step position-relative p-4 h-100">
                        <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            1
                        </div>
                        <h4 class="fw-bold mb-3">{{ __('user_journey_page.g1_title') }}</h4>
                        <p class="text-muted">{{ __('user_journey_page.g1_p') }}</p>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g1_l1') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g1_l2') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g1_l3') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g1_l4') }}</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="journey-step position-relative p-4 h-100">
                        <div class="step-number bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            2
                        </div>
                        <h4 class="fw-bold mb-3">{{ __('user_journey_page.g2_title') }}</h4>
                        <p class="text-muted">{{ __('user_journey_page.g2_p') }}</p>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g2_l1') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g2_l2') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g2_l3') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g2_l4') }}</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="journey-step position-relative p-4 h-100">
                        <div class="step-number bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            3
                        </div>
                        <h4 class="fw-bold mb-3">{{ __('user_journey_page.g3_title') }}</h4>
                        <p class="text-muted">{{ __('user_journey_page.g3_p') }}</p>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g3_l1') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g3_l2') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g3_l3') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g3_l4') }}</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="journey-step position-relative p-4 h-100">
                        <div class="step-number bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            4
                        </div>
                        <h4 class="fw-bold mb-3">{{ __('user_journey_page.g4_title') }}</h4>
                        <p class="text-muted">{{ __('user_journey_page.g4_p') }}</p>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g4_l1') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g4_l2') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g4_l3') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g4_l4') }}</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="journey-step position-relative p-4 h-100">
                        <div class="step-number bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            5
                        </div>
                        <h4 class="fw-bold mb-3">{{ __('user_journey_page.g5_title') }}</h4>
                        <p class="text-muted">{{ __('user_journey_page.g5_p') }}</p>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g5_l1') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g5_l2') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g5_l3') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g5_l4') }}</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="journey-step position-relative p-4 h-100">
                        <div class="step-number bg-purple text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            6
                        </div>
                        <h4 class="fw-bold mb-3">{{ __('user_journey_page.g6_title') }}</h4>
                        <p class="text-muted">{{ __('user_journey_page.g6_p') }}</p>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g6_l1') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g6_l2') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g6_l3') }}</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('user_journey_page.g6_l4') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Host Journey -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">{{ __('user_journey_page.host_title') }}</h2>
            <p class="lead text-muted">{{ __('user_journey_page.host_sub') }}</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="host-journey-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-user-plus text-primary' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">{{ __('user_journey_page.h1_title') }}</h5>
                    <p class="text-muted small">{{ __('user_journey_page.h1_p') }}</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="host-journey-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-edit text-success' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">{{ __('user_journey_page.h2_title') }}</h5>
                    <p class="text-muted small">{{ __('user_journey_page.h2_p') }}</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="host-journey-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-check-circle text-warning' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">{{ __('user_journey_page.h3_title') }}</h5>
                    <p class="text-muted small">{{ __('user_journey_page.h3_p') }}</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="host-journey-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-money text-info' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">{{ __('user_journey_page.h4_title') }}</h5>
                    <p class="text-muted small">{{ __('user_journey_page.h4_p') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Comparison -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">{{ __('user_journey_page.compare_title') }}</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="comparison-card p-4 border-0 shadow-sm rounded-4 h-100">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class='bx bx-check-circle text-success' style="font-size: 2rem;"></i>
                        <div>
                            <h5 class="fw-bold mb-2">{{ __('user_journey_page.cmp1_title') }}</h5>
                            <p class="text-muted mb-0">{{ __('user_journey_page.cmp1_p') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="comparison-card p-4 border-0 shadow-sm rounded-4 h-100">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class='bx bx-check-circle text-success' style="font-size: 2rem;"></i>
                        <div>
                            <h5 class="fw-bold mb-2">{{ __('user_journey_page.cmp2_title') }}</h5>
                            <p class="text-muted mb-0">{{ __('user_journey_page.cmp2_p') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="comparison-card p-4 border-0 shadow-sm rounded-4 h-100">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class='bx bx-check-circle text-success' style="font-size: 2rem;"></i>
                        <div>
                            <h5 class="fw-bold mb-2">{{ __('user_journey_page.cmp3_title') }}</h5>
                            <p class="text-muted mb-0">{{ __('user_journey_page.cmp3_p') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="comparison-card p-4 border-0 shadow-sm rounded-4 h-100">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class='bx bx-check-circle text-success' style="font-size: 2rem;"></i>
                        <div>
                            <h5 class="fw-bold mb-2">{{ __('user_journey_page.cmp4_title') }}</h5>
                            <p class="text-muted mb-0">{{ __('user_journey_page.cmp4_p') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container text-center text-white">
        <h2 class="display-5 fw-bold mb-3">{{ __('user_journey_page.cta_title') }}</h2>
        <p class="lead mb-4" style="opacity: 0.95;">{{ __('user_journey_page.cta_sub') }}</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="btn btn-light btn-lg px-5 rounded-pill fw-bold">
                <i class='bx bx-search-alt-2 me-2'></i>{{ __('user_journey_page.cta_search') }}
            </a>
            <a href="{{ route('property.create') }}" class="btn btn-outline-light btn-lg px-5 rounded-pill fw-bold">
                <i class='bx bx-plus-circle me-2'></i>{{ __('user_journey_page.cta_host') }}
            </a>
        </div>
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


