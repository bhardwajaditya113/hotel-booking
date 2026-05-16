@extends('frontend.main_master')
@section('main')

<!-- Page Header -->
<section class="page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-4 fw-bold mb-3">{{ __('host_page.hero_title') }}</h1>
                <p class="lead">{{ __('host_page.hero_sub') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Host Benefits -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">{{ __('host_page.why_title') }}</h2>
            <p class="lead text-muted">{{ __('host_page.why_sub') }}</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-money text-primary' style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">{{ __('host_page.b1_title') }}</h4>
                    <p class="text-muted">{{ __('host_page.b1_p') }}</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-calendar text-success' style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">{{ __('host_page.b2_title') }}</h4>
                    <p class="text-muted">{{ __('host_page.b2_p') }}</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-shield-check text-warning' style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">{{ __('host_page.b3_title') }}</h4>
                    <p class="text-muted">{{ __('host_page.b3_p') }}</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-trending-up text-info' style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">{{ __('host_page.b4_title') }}</h4>
                    <p class="text-muted">{{ __('host_page.b4_p') }}</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="icon-wrapper bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-star text-danger' style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">{{ __('host_page.b5_title') }}</h4>
                    <p class="text-muted">{{ __('host_page.b5_p') }}</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="icon-wrapper bg-purple bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-group text-purple' style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">{{ __('host_page.b6_title') }}</h4>
                    <p class="text-muted">{{ __('host_page.b6_p') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Hosting Types -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">{{ __('host_page.list_title') }}</h2>
            <p class="lead text-muted">{{ __('host_page.list_sub') }}</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="hosting-type-card p-4 h-100 bg-white border-0 shadow-sm rounded-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; flex-shrink: 0;">
                            <i class='bx bx-building text-primary' style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-2">{{ __('host_page.hotels_title') }}</h4>
                            <p class="text-muted mb-3">{{ __('host_page.hotels_p') }}</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('host_page.hotels_l1') }}</li>
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('host_page.hotels_l2') }}</li>
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('host_page.hotels_l3') }}</li>
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('host_page.hotels_l4') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="hosting-type-card p-4 h-100 bg-white border-0 shadow-sm rounded-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; flex-shrink: 0;">
                            <i class='bx bx-home text-success' style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-2">{{ __('host_page.unique_title') }}</h4>
                            <p class="text-muted mb-3">{{ __('host_page.unique_p') }}</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('host_page.unique_l1') }}</li>
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('host_page.unique_l2') }}</li>
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('host_page.unique_l3') }}</li>
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>{{ __('host_page.unique_l4') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Getting Started Steps -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">{{ __('host_page.start_title') }}</h2>
            <p class="lead text-muted">{{ __('host_page.start_sub') }}</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="step-card text-center p-4 h-100">
                    <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        1
                    </div>
                    <h5 class="fw-bold mb-3">{{ __('host_page.st1_title') }}</h5>
                    <p class="text-muted small">{{ __('host_page.st1_p') }}</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="step-card text-center p-4 h-100">
                    <div class="step-number bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        2
                    </div>
                    <h5 class="fw-bold mb-3">{{ __('host_page.st2_title') }}</h5>
                    <p class="text-muted small">{{ __('host_page.st2_p') }}</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="step-card text-center p-4 h-100">
                    <div class="step-number bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        3
                    </div>
                    <h5 class="fw-bold mb-3">{{ __('host_page.st3_title') }}</h5>
                    <p class="text-muted small">{{ __('host_page.st3_p') }}</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="step-card text-center p-4 h-100">
                    <div class="step-number bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        4
                    </div>
                    <h5 class="fw-bold mb-3">{{ __('host_page.st4_title') }}</h5>
                    <p class="text-muted small">{{ __('host_page.st4_p') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Host Earnings Calculator -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-white">
                <h2 class="display-5 fw-bold mb-4">{{ __('host_page.earn_title') }}</h2>
                <p class="lead mb-4" style="opacity: 0.95;">{{ __('host_page.earn_sub') }}</p>
                <div class="earning-stats">
                    <div class="stat-item mb-4">
                        <h3 class="display-6 fw-bold mb-2">{{ __('host_page.stat1_val') }}</h3>
                        <p class="mb-0" style="opacity: 0.9;">{{ __('host_page.stat1_p') }}</p>
                    </div>
                    <div class="stat-item mb-4">
                        <h3 class="display-6 fw-bold mb-2">{{ __('host_page.stat2_val') }}</h3>
                        <p class="mb-0" style="opacity: 0.9;">{{ __('host_page.stat2_p') }}</p>
                    </div>
                    <div class="stat-item">
                        <h3 class="display-6 fw-bold mb-2">{{ __('host_page.stat3_val') }}</h3>
                        <p class="mb-0" style="opacity: 0.9;">{{ __('host_page.stat3_p') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="calculator-card bg-white rounded-4 p-4 shadow-lg">
                    <h4 class="fw-bold mb-4 text-center">{{ __('host_page.calc_title') }}</h4>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('host_page.calc_prop_type') }}</label>
                        <select class="form-select" id="propertyType">
                            <option value="hotel">{{ __('host_page.opt_hotel') }}</option>
                            <option value="apartment">{{ __('host_page.opt_apt') }}</option>
                            <option value="villa">{{ __('host_page.opt_villa') }}</option>
                            <option value="home">{{ __('host_page.opt_home') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('host_page.calc_price') }}</label>
                        <input type="number" class="form-control" id="pricePerNight" value="3000" min="500" max="50000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('host_page.calc_occ') }}</label>
                        <input type="number" class="form-control" id="occupancy" value="70" min="0" max="100">
                    </div>
                    <div class="result-box bg-primary bg-opacity-10 rounded p-3 mb-3">
                        <p class="mb-1 text-muted small">{{ __('host_page.calc_result_label') }}</p>
                        <h3 class="mb-0 fw-bold text-primary" id="monthlyEarnings">₹63,000</h3>
                    </div>
                    <button class="btn btn-primary w-100 rounded-pill" onclick="calculateEarnings()">
                        {{ __('host_page.calc_btn') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-3">{{ __('host_page.cta_title') }}</h2>
        <p class="lead text-muted mb-4">{{ __('host_page.cta_sub') }}</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            @auth
            <a href="{{ route('property.create') }}" class="btn btn-primary btn-lg px-5 rounded-pill fw-bold">
                <i class='bx bx-plus-circle me-2'></i>{{ __('host_page.cta_list') }}
            </a>
            @else
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5 rounded-pill fw-bold">
                <i class='bx bx-user-plus me-2'></i>{{ __('host_page.cta_signup') }}
            </a>
            @endauth
            <a href="{{ route('how-it-works') }}" class="btn btn-outline-primary btn-lg px-5 rounded-pill fw-bold">
                {{ __('host_page.cta_learn') }} <i class='bx bx-info-circle ms-2'></i>
            </a>
        </div>
    </div>
</section>

<script>
function calculateEarnings() {
    const price = parseFloat(document.getElementById('pricePerNight').value) || 3000;
    const occupancy = parseFloat(document.getElementById('occupancy').value) || 70;
    const daysInMonth = 30;
    const monthlyEarnings = Math.round((price * daysInMonth * occupancy) / 100);
    document.getElementById('monthlyEarnings').textContent = '₹' + monthlyEarnings.toLocaleString('en-IN');
}
</script>

<style>
.bg-purple {
    background-color: #6f42c1 !important;
}
.text-purple {
    color: #6f42c1 !important;
}
</style>

@endsection


