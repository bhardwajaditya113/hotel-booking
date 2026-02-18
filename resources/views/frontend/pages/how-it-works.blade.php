@extends('frontend.main_master')
@section('main')

<!-- Page Header -->
<section class="page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-4 fw-bold mb-3">How It Works</h1>
                <p class="lead">Simple steps to find and book your perfect accommodation</p>
            </div>
        </div>
    </div>
</section>

<!-- For Guests Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">For Guests</h2>
            <p class="lead text-muted">Book your stay in 3 easy steps</p>
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
                    <h3 class="fw-bold mb-3">Search & Discover</h3>
                    <p class="text-muted">Use our powerful search to find hotels and unique stays. Filter by location, dates, price, amenities, and property type. Browse verified properties with real photos and reviews.</p>
                    <ul class="list-unstyled text-start mt-4">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Search by city or location</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Filter by price range</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>View on map</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Read verified reviews</li>
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
                    <h3 class="fw-bold mb-3">Book Your Stay</h3>
                    <p class="text-muted">Choose your preferred property and room. Book instantly or request to book. Secure your reservation with our trusted payment system.</p>
                    <ul class="list-unstyled text-start mt-4">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Instant booking available</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Request to book option</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Secure payment gateway</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Instant confirmation</li>
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
                    <h3 class="fw-bold mb-3">Enjoy & Review</h3>
                    <p class="text-muted">Check in and enjoy your stay. After checkout, share your experience with reviews and ratings to help other travelers.</p>
                    <ul class="list-unstyled text-start mt-4">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Easy check-in process</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>24/7 customer support</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Leave reviews & ratings</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Earn loyalty points</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="text-center">
            <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="btn btn-primary btn-lg px-5 rounded-pill fw-bold">
                Start Searching <i class='bx bx-arrow-right ms-2'></i>
            </a>
        </div>
    </div>
</section>

<!-- For Hosts Section -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">For Hosts</h2>
            <p class="lead text-muted">Start earning by listing your property</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="host-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-edit text-primary' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Create Listing</h5>
                    <p class="text-muted small">Sign up and create your property listing. Add photos, description, amenities, and set your pricing.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="host-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-check-circle text-success' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Get Verified</h5>
                    <p class="text-muted small">Our team reviews and verifies your property. Get verified badge to build trust with guests.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="host-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-calendar text-warning' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Receive Bookings</h5>
                    <p class="text-muted small">Start receiving booking requests. Accept or enable instant booking. Manage your calendar easily.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="host-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-money text-info' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Earn Money</h5>
                    <p class="text-muted small">Get paid securely. Track your earnings, manage payouts, and grow your hosting business.</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <a href="{{ route('property.create') }}" class="btn btn-primary btn-lg px-5 rounded-pill fw-bold me-3">
                List Your Property <i class='bx bx-plus-circle ms-2'></i>
            </a>
            <a href="{{ route('how-it-works-host') }}" class="btn btn-outline-primary btn-lg px-5 rounded-pill fw-bold">
                Learn More <i class='bx bx-info-circle ms-2'></i>
            </a>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="display-5 fw-bold mb-4">Why Travelers Love Us</h2>
                <div class="benefit-item d-flex gap-3 mb-4">
                    <div class="icon-box bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; flex-shrink: 0;">
                        <i class='bx bx-shield-check text-primary' style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-2">Verified Properties</h5>
                        <p class="text-muted mb-0">Every property is verified for safety, quality, and accuracy. Book with confidence.</p>
                    </div>
                </div>
                <div class="benefit-item d-flex gap-3 mb-4">
                    <div class="icon-box bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; flex-shrink: 0;">
                        <i class='bx bx-message-dots text-success' style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-2">Direct Communication</h5>
                        <p class="text-muted mb-0">Message hosts directly before and during your stay. Get instant responses.</p>
                    </div>
                </div>
                <div class="benefit-item d-flex gap-3 mb-4">
                    <div class="icon-box bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; flex-shrink: 0;">
                        <i class='bx bx-refresh text-warning' style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-2">Flexible Cancellation</h5>
                        <p class="text-muted mb-0">Free cancellation on most properties. Clear cancellation policies for every booking.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="image-wrapper position-relative">
                    <img src="{{ asset('frontend/assets/img/room/room-img1.jpg') }}" alt="Benefits" class="img-fluid rounded-4 shadow-lg">
                    <div class="stats-card position-absolute bg-white rounded-4 p-4 shadow-lg" style="bottom: -20px; right: -20px; max-width: 250px;">
                        <h3 class="display-6 fw-bold text-primary mb-2">4.8/5</h3>
                        <p class="mb-0 text-muted">Average Rating</p>
                        <hr class="my-2">
                        <p class="mb-0 small text-muted"><i class='bx bx-star text-warning'></i> From 10,000+ reviews</p>
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
        <p class="lead mb-4" style="opacity: 0.95;">Join thousands of happy travelers and hosts</p>
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


