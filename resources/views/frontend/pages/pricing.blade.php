@extends('frontend.main_master')
@section('main')

<!-- Page Header -->
<section class="page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-4 fw-bold mb-3">Transparent Pricing</h1>
                <p class="lead">No hidden fees. Clear pricing for guests and hosts.</p>
            </div>
        </div>
    </div>
</section>

<!-- Guest Pricing -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">For Guests</h2>
            <p class="lead text-muted">What you see is what you pay</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4">
                <div class="pricing-card border-0 shadow-lg rounded-4 p-4 h-100 text-center">
                    <div class="pricing-header mb-4">
                        <h3 class="fw-bold mb-2">Free to Book</h3>
                        <div class="price-display">
                            <span class="display-4 fw-bold text-primary">₹0</span>
                            <p class="text-muted mb-0">No booking fees</p>
                        </div>
                    </div>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>No booking fees</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Transparent pricing</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Secure payments</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Free cancellation</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>24/7 support</li>
                    </ul>
                    <div class="pricing-note bg-light rounded p-3">
                        <p class="mb-0 small text-muted">
                            <strong>Note:</strong> You only pay the property price shown. Taxes and service fees (if any) are clearly displayed before booking.
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
            <h2 class="display-5 fw-bold mb-3">For Hosts</h2>
            <p class="lead text-muted">Simple, competitive commission structure</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="pricing-card border-0 shadow-lg rounded-4 p-4 h-100 text-center">
                    <div class="pricing-badge bg-primary text-white rounded-pill px-3 py-1 mb-3 d-inline-block">
                        Most Popular
                    </div>
                    <h3 class="fw-bold mb-3">Standard Plan</h3>
                    <div class="price-display mb-4">
                        <span class="display-4 fw-bold text-primary">15%</span>
                        <p class="text-muted mb-0">per booking</p>
                    </div>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Property listing</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Booking management</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Payment processing</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Customer support</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Marketing tools</li>
                    </ul>
                    <a href="{{ route('property.create') }}" class="btn btn-primary w-100 rounded-pill fw-bold">
                        Get Started
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="pricing-card border-0 shadow-lg rounded-4 p-4 h-100 text-center position-relative" style="border: 3px solid #667eea !important;">
                    <div class="pricing-badge bg-success text-white rounded-pill px-3 py-1 mb-3 d-inline-block">
                        Best Value
                    </div>
                    <h3 class="fw-bold mb-3">Premium Plan</h3>
                    <div class="price-display mb-4">
                        <span class="display-4 fw-bold text-success">12%</span>
                        <p class="text-muted mb-0">per booking</p>
                        <p class="text-success small mb-0">Save 3%</p>
                    </div>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Everything in Standard</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Priority placement</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Advanced analytics</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Dedicated support</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Marketing boost</li>
                    </ul>
                    <a href="{{ route('property.create') }}" class="btn btn-success w-100 rounded-pill fw-bold">
                        Choose Premium
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="pricing-card border-0 shadow-lg rounded-4 p-4 h-100 text-center">
                    <h3 class="fw-bold mb-3">Enterprise</h3>
                    <div class="price-display mb-4">
                        <span class="display-4 fw-bold text-info">Custom</span>
                        <p class="text-muted mb-0">Contact us</p>
                    </div>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Everything in Premium</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>API access</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>White-label options</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Custom integrations</li>
                        <li class="mb-3"><i class='bx bx-check-circle text-success me-2 fs-5'></i>Account manager</li>
                    </ul>
                    <a href="{{ route('contact.us') }}" class="btn btn-outline-primary w-100 rounded-pill fw-bold">
                        Contact Sales
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
                <h2 class="display-5 fw-bold text-center mb-5">Pricing FAQ</h2>
                
                <div class="accordion" id="pricingFAQ">
                    <div class="accordion-item border-0 shadow-sm mb-3 rounded">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Are there any hidden fees for guests?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#pricingFAQ">
                            <div class="accordion-body">
                                No hidden fees. The price you see is the price you pay. Any taxes or service fees are clearly displayed before booking.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3 rounded">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                When do hosts get paid?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#pricingFAQ">
                            <div class="accordion-body">
                                Hosts receive payment 24 hours after guest check-in. Payments are processed securely and can be transferred to your bank account or digital wallet.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3 rounded">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Can I change my pricing plan?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#pricingFAQ">
                            <div class="accordion-body">
                                Yes, you can upgrade or downgrade your plan at any time. Changes take effect on your next booking cycle.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3 rounded">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                What payment methods are accepted?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#pricingFAQ">
                            <div class="accordion-body">
                                We accept all major credit cards, debit cards, UPI, net banking, and digital wallets. All payments are processed securely.
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
        <h2 class="display-5 fw-bold mb-3">Ready to Get Started?</h2>
        <p class="lead mb-4" style="opacity: 0.95;">Join thousands of satisfied guests and hosts</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="btn btn-light btn-lg px-5 rounded-pill fw-bold">
                <i class='bx bx-search-alt-2 me-2'></i>Search Properties
            </a>
            <a href="{{ route('property.create') }}" class="btn btn-outline-light btn-lg px-5 rounded-pill fw-bold">
                <i class='bx bx-plus-circle me-2'></i>Become a Host
            </a>
        </div>
    </div>
</section>

@endsection


