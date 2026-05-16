@extends('frontend.main_master')
@section('main')

<!-- Page Header -->
<section class="page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-4 fw-bold mb-3">{{ __('pricing_page.hero_title') }}</h1>
                <p class="lead">{{ __('pricing_page.hero_sub') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Guest Pricing -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">{{ __('pricing_page.guests_section_title') }}</h2>
            <p class="lead text-muted">{{ __('pricing_page.guests_section_sub') }}</p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-lg-4">
                <div class="pricing-card border-0 shadow-lg rounded-4 p-4 h-100 text-center">
                    <div class="pricing-header mb-4">
                        <h3 class="fw-bold mb-2">{{ __('pricing_page.guest_card_title') }}</h3>
                        <div class="price-display">
                            <span class="display-4 fw-bold text-primary">₹0</span>
                            <p class="text-muted mb-0">{{ __('pricing_page.guest_price_label') }}</p>
                        </div>
                    </div>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.guest_li1') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.guest_li2') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.guest_li3') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.guest_li4') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.guest_li5') }}</li>
                    </ul>
                    <div class="pricing-note bg-light rounded p-3">
                        <p class="mb-0 small text-muted">
                            <strong>{{ __('pricing_page.guest_note_label') }}</strong> {{ __('pricing_page.guest_note_body') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Host Pricing -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">{{ __('pricing_page.hosts_section_title') }}</h2>
            <p class="lead text-muted">{{ __('pricing_page.hosts_section_sub') }}</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="pricing-card border-0 shadow-lg rounded-4 p-4 h-100 text-center">
                    <div class="pricing-badge bg-primary text-white rounded-pill px-3 py-1 mb-3 d-inline-block">
                        {{ __('pricing_page.badge_popular') }}
                    </div>
                    <h3 class="fw-bold mb-3">{{ __('pricing_page.plan_standard') }}</h3>
                    <div class="price-display mb-4">
                        <span class="display-4 fw-bold text-primary">15%</span>
                        <p class="text-muted mb-0">{{ __('pricing_page.per_booking') }}</p>
                    </div>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.std_li1') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.std_li2') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.std_li3') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.std_li4') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.std_li5') }}</li>
                    </ul>
                    <a href="{{ route('property.create') }}" class="btn btn-primary w-100 rounded-pill fw-bold">
                        {{ __('pricing_page.get_started') }}
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="pricing-card border-0 shadow-lg rounded-4 p-4 h-100 text-center position-relative" style="border: 3px solid #667eea !important;">
                    <div class="pricing-badge bg-success text-white rounded-pill px-3 py-1 mb-3 d-inline-block">
                        {{ __('pricing_page.badge_best') }}
                    </div>
                    <h3 class="fw-bold mb-3">{{ __('pricing_page.plan_premium') }}</h3>
                    <div class="price-display mb-4">
                        <span class="display-4 fw-bold text-success">12%</span>
                        <p class="text-muted mb-0">{{ __('pricing_page.per_booking') }}</p>
                        <p class="text-success small mb-0">{{ __('pricing_page.save_pct') }}</p>
                    </div>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.prem_li1') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.prem_li2') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.prem_li3') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.prem_li4') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.prem_li5') }}</li>
                    </ul>
                    <a href="{{ route('property.create') }}" class="btn btn-success w-100 rounded-pill fw-bold">
                        {{ __('pricing_page.choose_premium') }}
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="pricing-card border-0 shadow-lg rounded-4 p-4 h-100 text-center">
                    <h3 class="fw-bold mb-3">{{ __('pricing_page.plan_enterprise') }}</h3>
                    <div class="price-display mb-4">
                        <span class="display-4 fw-bold text-info">{{ __('pricing_page.custom') }}</span>
                        <p class="text-muted mb-0">{{ __('pricing_page.contact_us') }}</p>
                    </div>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.ent_li1') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.ent_li2') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.ent_li3') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.ent_li4') }}</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>{{ __('pricing_page.ent_li5') }}</li>
                    </ul>
                    <a href="{{ route('contact.us') }}" class="btn btn-outline-primary w-100 rounded-pill fw-bold">
                        {{ __('pricing_page.contact_sales') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing FAQ -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold text-center mb-5">{{ __('pricing_page.faq_title') }}</h2>

                <div class="accordion" id="pricingFAQ">
                    <div class="accordion-item border-0 shadow-sm mb-3 rounded">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                {{ __('pricing_page.faq1_q') }}
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#pricingFAQ">
                            <div class="accordion-body">
                                {{ __('pricing_page.faq1_a') }}
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 shadow-sm mb-3 rounded">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                {{ __('pricing_page.faq2_q') }}
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#pricingFAQ">
                            <div class="accordion-body">
                                {{ __('pricing_page.faq2_a') }}
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 shadow-sm mb-3 rounded">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                {{ __('pricing_page.faq3_q') }}
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#pricingFAQ">
                            <div class="accordion-body">
                                {{ __('pricing_page.faq3_a') }}
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 shadow-sm mb-3 rounded">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                {{ __('pricing_page.faq4_q') }}
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#pricingFAQ">
                            <div class="accordion-body">
                                {{ __('pricing_page.faq4_a') }}
                            </div>
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
        <h2 class="display-5 fw-bold mb-3">{{ __('pricing_page.cta_title') }}</h2>
        <p class="lead mb-4" style="opacity: 0.95;">{{ __('pricing_page.cta_sub') }}</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="btn btn-light btn-lg px-5 rounded-pill fw-bold">
                <i class='bx bx-search-alt-2 me-2'></i>{{ __('pricing_page.cta_search') }}
            </a>
            <a href="{{ route('property.create') }}" class="btn btn-outline-light btn-lg px-5 rounded-pill fw-bold">
                <i class='bx bx-plus-circle me-2'></i>{{ __('pricing_page.cta_host') }}
            </a>
        </div>
    </div>
</section>

@endsection
