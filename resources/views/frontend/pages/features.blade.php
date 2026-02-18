@extends('frontend.main_master')
@section('main')

<!-- Page Header -->
<section class="page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-4 fw-bold mb-3">Platform Features</h1>
                <p class="lead">Everything you need for the perfect booking experience</p>
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
                    <h4 class="fw-bold mb-3">Advanced Search</h4>
                    <p class="text-muted">Powerful search filters to find exactly what you're looking for. Filter by location, price, amenities, property type, and more.</p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Map-based search</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Price range filters</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Amenity filters</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Instant availability</li>
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
                    <h4 class="fw-bold mb-3">Direct Messaging</h4>
                    <p class="text-muted">Communicate directly with hosts before and during your stay. Get instant responses and answers to your questions.</p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Real-time messaging</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Property context</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Booking integration</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Message history</li>
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
                    <h4 class="fw-bold mb-3">Verified Properties</h4>
                    <p class="text-muted">All properties go through our verification process. Book with confidence knowing every listing is verified.</p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Property verification</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Host verification</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Photo verification</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Superhost program</li>
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
                    <h4 class="fw-bold mb-3">Secure Payments</h4>
                    <p class="text-muted">Multiple payment options with industry-leading security. Your payment information is always protected.</p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Multiple payment methods</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>SSL encryption</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Secure checkout</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Payment protection</li>
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
                    <h4 class="fw-bold mb-3">Flexible Cancellation</h4>
                    <p class="text-muted">Clear cancellation policies for every property. Free cancellation on most bookings.</p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Free cancellation options</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Clear policies</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Easy cancellation</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Quick refunds</li>
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
                    <h4 class="fw-bold mb-3">Reviews & Ratings</h4>
                    <p class="text-muted">Read verified reviews from real guests. Share your experience to help others.</p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Verified reviews</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Photo reviews</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Rating breakdown</li>
                        <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Helpful votes</li>
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
            <h2 class="display-5 fw-bold mb-3">More Great Features</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <i class='bx bx-wifi text-primary' style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mt-3 mb-2">Free WiFi</h5>
                    <p class="text-muted small">Available at most properties</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <i class='bx bx-support text-success' style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mt-3 mb-2">24/7 Support</h5>
                    <p class="text-muted small">Always here to help</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <i class='bx bx-gift text-warning' style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mt-3 mb-2">Loyalty Rewards</h5>
                    <p class="text-muted small">Earn points on every booking</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <i class='bx bx-mobile text-info' style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mt-3 mb-2">Mobile App</h5>
                    <p class="text-muted small">Book on the go</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container text-center text-white">
        <h2 class="display-5 fw-bold mb-3">Experience All Features</h2>
        <p class="lead mb-4" style="opacity: 0.95;">Start exploring amazing properties today</p>
        <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="btn btn-light btn-lg px-5 rounded-pill fw-bold">
            <i class='bx bx-search-alt-2 me-2'></i>Start Searching
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


