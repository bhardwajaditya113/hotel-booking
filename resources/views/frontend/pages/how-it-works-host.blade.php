@extends('frontend.main_master')
@section('main')

<!-- Page Header -->
<section class="page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-4 fw-bold mb-3">Become a Host</h1>
                <p class="lead">Turn your space into income. Join our community of successful hosts.</p>
            </div>
        </div>
    </div>
</section>

<!-- Host Benefits -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Why Host With Us?</h2>
            <p class="lead text-muted">Everything you need to succeed as a host</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-money text-primary' style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Earn Extra Income</h4>
                    <p class="text-muted">Set your own prices and earn money from your property. Get paid securely and on time.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-calendar text-success' style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Flexible Schedule</h4>
                    <p class="text-muted">You control your calendar. Block dates when you're not available. Accept or decline bookings.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-shield-check text-warning' style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Protection & Support</h4>
                    <p class="text-muted">Host protection insurance, secure payments, and 24/7 support for you and your guests.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-trending-up text-info' style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Grow Your Business</h4>
                    <p class="text-muted">Access analytics, marketing tools, and resources to grow your hosting business.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="icon-wrapper bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-star text-danger' style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Superhost Program</h4>
                    <p class="text-muted">Become a Superhost and get priority placement, special badges, and exclusive benefits.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="icon-wrapper bg-purple bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class='bx bx-group text-purple' style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Large Community</h4>
                    <p class="text-muted">Join thousands of hosts. Get tips, share experiences, and learn from the community.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Hosting Types -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">What Can You List?</h2>
            <p class="lead text-muted">We support all types of properties</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="hosting-type-card p-4 h-100 bg-white border-0 shadow-sm rounded-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; flex-shrink: 0;">
                            <i class='bx bx-building text-primary' style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-2">Hotels & Resorts</h4>
                            <p class="text-muted mb-3">Perfect for hotel chains, resorts, and hospitality businesses. List multiple rooms, manage inventory, and scale your business.</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Multiple room management</li>
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Centralized booking system</li>
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Business analytics</li>
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Bulk operations</li>
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
                            <h4 class="fw-bold mb-2">Unique Stays</h4>
                            <p class="text-muted mb-3">Ideal for individual hosts with apartments, villas, homes, or unique spaces. Share your space and earn extra income.</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Individual property listing</li>
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Request-to-book option</li>
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Direct guest communication</li>
                                <li class="mb-2"><i class='bx bx-check text-success me-2'></i>Superhost opportunities</li>
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
            <h2 class="display-5 fw-bold mb-3">Getting Started is Easy</h2>
            <p class="lead text-muted">List your property in minutes</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="step-card text-center p-4 h-100">
                    <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        1
                    </div>
                    <h5 class="fw-bold mb-3">Create Account</h5>
                    <p class="text-muted small">Sign up as a host. It's free and takes less than a minute.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="step-card text-center p-4 h-100">
                    <div class="step-number bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        2
                    </div>
                    <h5 class="fw-bold mb-3">List Your Property</h5>
                    <p class="text-muted small">Add photos, description, amenities, and set your pricing.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="step-card text-center p-4 h-100">
                    <div class="step-number bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        3
                    </div>
                    <h5 class="fw-bold mb-3">Get Verified</h5>
                    <p class="text-muted small">Our team reviews your listing. Get verified badge for trust.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="step-card text-center p-4 h-100">
                    <div class="step-number bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        4
                    </div>
                    <h5 class="fw-bold mb-3">Start Earning</h5>
                    <p class="text-muted small">Receive bookings and start earning money from your property.</p>
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
                <h2 class="display-5 fw-bold mb-4">Earning Potential</h2>
                <p class="lead mb-4" style="opacity: 0.95;">See how much you could earn by hosting</p>
                <div class="earning-stats">
                    <div class="stat-item mb-4">
                        <h3 class="display-6 fw-bold mb-2">₹50,000+</h3>
                        <p class="mb-0" style="opacity: 0.9;">Average monthly earnings per property</p>
                    </div>
                    <div class="stat-item mb-4">
                        <h3 class="display-6 fw-bold mb-2">85%</h3>
                        <p class="mb-0" style="opacity: 0.9;">Occupancy rate for verified properties</p>
                    </div>
                    <div class="stat-item">
                        <h3 class="display-6 fw-bold mb-2">24/7</h3>
                        <p class="mb-0" style="opacity: 0.9;">Support for all your hosting needs</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="calculator-card bg-white rounded-4 p-4 shadow-lg">
                    <h4 class="fw-bold mb-4 text-center">Earnings Calculator</h4>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Property Type</label>
                        <select class="form-select" id="propertyType">
                            <option value="hotel">Hotel Room</option>
                            <option value="apartment">Apartment</option>
                            <option value="villa">Villa</option>
                            <option value="home">Home</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Price per Night (₹)</label>
                        <input type="number" class="form-control" id="pricePerNight" value="3000" min="500" max="50000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Occupancy Rate (%)</label>
                        <input type="number" class="form-control" id="occupancy" value="70" min="0" max="100">
                    </div>
                    <div class="result-box bg-primary bg-opacity-10 rounded p-3 mb-3">
                        <p class="mb-1 text-muted small">Estimated Monthly Earnings</p>
                        <h3 class="mb-0 fw-bold text-primary" id="monthlyEarnings">₹63,000</h3>
                    </div>
                    <button class="btn btn-primary w-100 rounded-pill" onclick="calculateEarnings()">
                        Calculate Earnings
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-3">Ready to Start Hosting?</h2>
        <p class="lead text-muted mb-4">Join thousands of successful hosts today</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            @auth
            <a href="{{ route('property.create') }}" class="btn btn-primary btn-lg px-5 rounded-pill fw-bold">
                <i class='bx bx-plus-circle me-2'></i>List Your Property
            </a>
            @else
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5 rounded-pill fw-bold">
                <i class='bx bx-user-plus me-2'></i>Sign Up to Host
            </a>
            @endauth
            <a href="{{ route('how-it-works') }}" class="btn btn-outline-primary btn-lg px-5 rounded-pill fw-bold">
                Learn More <i class='bx bx-info-circle ms-2'></i>
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


