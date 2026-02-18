@extends('frontend.main_master')
@section('main')

<!-- Page Header -->
<section class="page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-4 fw-bold mb-3">Your Journey Starts Here</h1>
                <p class="lead">From discovery to booking to enjoying your stay</p>
            </div>
        </div>
    </div>
</section>

<!-- Guest Journey -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Guest Journey</h2>
            <p class="lead text-muted">Your complete booking experience</p>
        </div>
        
        <div class="journey-timeline">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="journey-step position-relative p-4 h-100">
                        <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            1
                        </div>
                        <h4 class="fw-bold mb-3">Discover</h4>
                        <p class="text-muted">Browse thousands of verified properties. Use our advanced search to find the perfect stay.</p>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Search by location</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Filter by preferences</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>View on map</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Read reviews</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="journey-step position-relative p-4 h-100">
                        <div class="step-number bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            2
                        </div>
                        <h4 class="fw-bold mb-3">Explore</h4>
                        <p class="text-muted">View detailed property pages with photos, amenities, host information, and reviews.</p>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>High-quality photos</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Amenity details</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Host profile</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Verified reviews</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="journey-step position-relative p-4 h-100">
                        <div class="step-number bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            3
                        </div>
                        <h4 class="fw-bold mb-3">Connect</h4>
                        <p class="text-muted">Message hosts directly to ask questions, clarify details, or discuss special requests.</p>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Direct messaging</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Quick responses</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Property context</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Booking integration</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="journey-step position-relative p-4 h-100">
                        <div class="step-number bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            4
                        </div>
                        <h4 class="fw-bold mb-3">Book</h4>
                        <p class="text-muted">Secure your reservation with instant booking or request to book. Multiple payment options available.</p>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Instant booking</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Request to book</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Secure payment</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Instant confirmation</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="journey-step position-relative p-4 h-100">
                        <div class="step-number bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            5
                        </div>
                        <h4 class="fw-bold mb-3">Stay</h4>
                        <p class="text-muted">Check in and enjoy your stay. Access 24/7 support if you need anything during your visit.</p>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Easy check-in</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>24/7 support</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Property access</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Host communication</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="journey-step position-relative p-4 h-100">
                        <div class="step-number bg-purple text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            6
                        </div>
                        <h4 class="fw-bold mb-3">Share</h4>
                        <p class="text-muted">After checkout, share your experience with reviews and ratings. Help other travelers make informed decisions.</p>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Write reviews</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Upload photos</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Rate experience</li>
                            <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Earn loyalty points</li>
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
            <h2 class="display-5 fw-bold mb-3">Host Journey</h2>
            <p class="lead text-muted">From listing to earning</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="host-journey-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-user-plus text-primary' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Sign Up</h5>
                    <p class="text-muted small">Create your free host account in minutes. No credit card required.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="host-journey-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-edit text-success' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Create Listing</h5>
                    <p class="text-muted small">Add property details, photos, amenities, and set your pricing strategy.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="host-journey-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-check-circle text-warning' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Get Verified</h5>
                    <p class="text-muted small">Our team reviews your listing. Get verified badge to build trust.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="host-journey-step text-center p-4 h-100">
                    <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-money text-info' style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Start Earning</h5>
                    <p class="text-muted small">Receive bookings and start earning. Get paid securely and on time.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Comparison -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Why Travelers Choose Us</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="comparison-card p-4 border-0 shadow-sm rounded-4 h-100">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class='bx bx-check-circle text-success' style="font-size: 2rem;"></i>
                        <div>
                            <h5 class="fw-bold mb-2">Verified Properties Only</h5>
                            <p class="text-muted mb-0">Every property is verified for safety and accuracy. Book with complete confidence.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="comparison-card p-4 border-0 shadow-sm rounded-4 h-100">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class='bx bx-check-circle text-success' style="font-size: 2rem;"></i>
                        <div>
                            <h5 class="fw-bold mb-2">Best Price Guarantee</h5>
                            <p class="text-muted mb-0">We ensure you get the best prices. Find a lower price? We'll match it.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="comparison-card p-4 border-0 shadow-sm rounded-4 h-100">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class='bx bx-check-circle text-success' style="font-size: 2rem;"></i>
                        <div>
                            <h5 class="fw-bold mb-2">24/7 Customer Support</h5>
                            <p class="text-muted mb-0">Round-the-clock support for all your needs. We're always here to help.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="comparison-card p-4 border-0 shadow-sm rounded-4 h-100">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class='bx bx-check-circle text-success' style="font-size: 2rem;"></i>
                        <div>
                            <h5 class="fw-bold mb-2">Flexible Cancellation</h5>
                            <p class="text-muted mb-0">Free cancellation on most properties. Clear policies for every booking.</p>
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
        <h2 class="display-5 fw-bold mb-3">Start Your Journey Today</h2>
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

<style>
.bg-purple {
    background-color: #6f42c1 !important;
}
.text-purple {
    color: #6f42c1 !important;
}
</style>

@endsection


