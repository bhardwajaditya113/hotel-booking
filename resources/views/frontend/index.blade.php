@extends('frontend.main_master')
@section('main')

<!-- Hero Section with Modern Design -->
<section class="hero-section position-relative overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 90vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content text-white">
                    <h1 class="display-3 fw-bold mb-4 animate__animated animate__fadeInUp" style="font-size: 3.5rem; line-height: 1.2;">
                        Find Your Perfect Stay
                        <span class="d-block text-warning">Anywhere, Anytime</span>
                    </h1>
                    <p class="lead mb-4 animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 1.25rem; opacity: 0.95;">
                        Discover amazing hotels and unique stays. Book with confidence on the world's most trusted accommodation platform.
                    </p>
                    <div class="hero-buttons d-flex gap-3 flex-wrap animate__animated animate__fadeInUp animate__delay-2s">
                        <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="btn btn-light btn-lg px-5 py-3 rounded-pill fw-bold">
                            <i class='bx bx-search-alt-2 me-2'></i>Explore Properties
                        </a>
                        <a href="#how-it-works" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill fw-bold">
                            <i class='bx bx-play-circle me-2'></i>How It Works
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image position-relative">
                    <div class="floating-card position-absolute" style="top: 10%; right: 10%; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: float 3s ease-in-out infinite;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class='bx bx-star fs-4'></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">4.8 Rating</h5>
                                <p class="mb-0 text-muted small">From 10K+ Reviews</p>
                            </div>
                        </div>
                    </div>
                    <div class="floating-card position-absolute" style="bottom: 10%; left: 10%; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: float 3s ease-in-out infinite 1.5s;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class='bx bx-check-circle fs-4'></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Verified</h5>
                                <p class="mb-0 text-muted small">100% Trusted</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="wave-divider position-absolute bottom-0 w-100">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
        </svg>
    </div>
</section>

<!-- Enhanced Search Form -->
<section class="search-form-section py-5" style="background: white; margin-top: -50px; position: relative; z-index: 10;">
    <div class="container">
        <div class="search-card shadow-lg border-0 rounded-4 p-4" style="background: white;">
            <form method="get" action="{{ route('search.results') }}" id="homeSearchForm">
                <input type="hidden" name="search_mode" value="properties">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold text-dark mb-2">
                                <i class='bx bx-calendar me-2 text-primary'></i>Check In
                            </label>
                            <input type="date" name="check_in" class="form-control form-control-lg" 
                                   value="{{ request('check_in', date('Y-m-d')) }}" 
                                   min="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold text-dark mb-2">
                                <i class='bx bx-calendar-check me-2 text-primary'></i>Check Out
                            </label>
                            <input type="date" name="check_out" class="form-control form-control-lg" 
                                   value="{{ request('check_out', date('Y-m-d', strtotime('+1 day'))) }}" 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold text-dark mb-2">
                                <i class='bx bx-user me-2 text-primary'></i>Guests
                            </label>
                            <select name="guests" class="form-select form-select-lg">
                                @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ request('guests') == $i ? 'selected' : '' }}>
                                    {{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}
                                </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold text-dark mb-2">
                                <i class='bx bx-map me-2 text-primary'></i>Location
                            </label>
                            <input type="text" name="city" class="form-control form-control-lg" 
                                   placeholder="City or Area" value="{{ request('city') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12">
                        <div class="form-group">
                            <label class="form-label fw-bold text-dark mb-2 d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">
                                <i class='bx bx-search-alt-2 me-2'></i>Search
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3">Why Choose Us?</h2>
            <p class="lead text-muted">Experience the best in hospitality and accommodation</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center p-4 h-100 border-0 shadow-sm rounded-4 hover-lift">
                    <div class="feature-icon mb-3">
                        <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class='bx bx-shield-check text-primary' style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3">Verified Properties</h4>
                    <p class="text-muted">All properties are verified and trusted for your safety and comfort.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center p-4 h-100 border-0 shadow-sm rounded-4 hover-lift">
                    <div class="feature-icon mb-3">
                        <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class='bx bx-credit-card text-success' style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3">Secure Payments</h4>
                    <p class="text-muted">Multiple payment options with 100% secure transactions.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center p-4 h-100 border-0 shadow-sm rounded-4 hover-lift">
                    <div class="feature-icon mb-3">
                        <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class='bx bx-support text-warning' style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3">24/7 Support</h4>
                    <p class="text-muted">Round-the-clock customer support for all your needs.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center p-4 h-100 border-0 shadow-sm rounded-4 hover-lift">
                    <div class="feature-icon mb-3">
                        <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class='bx bx-money text-info' style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3">Best Prices</h4>
                    <p class="text-muted">Competitive pricing with exclusive deals and discounts.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="how-it-works-section py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3">How It Works</h2>
            <p class="lead text-muted">Book your perfect stay in just 3 simple steps</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="step-card text-center p-4 position-relative">
                    <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        1
                    </div>
                    <div class="step-icon mb-3">
                        <i class='bx bx-search-alt-2 text-primary' style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Search & Explore</h4>
                    <p class="text-muted">Browse through thousands of verified hotels and unique stays. Filter by location, price, amenities, and more.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="step-card text-center p-4 position-relative">
                    <div class="step-number bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        2
                    </div>
                    <div class="step-icon mb-3">
                        <i class='bx bx-calendar-check text-success' style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Book Instantly</h4>
                    <p class="text-muted">Choose your dates, select your room, and book instantly. Or request to book and get confirmed by the host.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="step-card text-center p-4 position-relative">
                    <div class="step-number bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        3
                    </div>
                    <div class="step-icon mb-3">
                        <i class='bx bx-happy text-warning' style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Enjoy Your Stay</h4>
                    <p class="text-muted">Check in, relax, and enjoy your perfect stay. Share your experience with reviews and ratings.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Properties Showcase -->
<section class="properties-showcase py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="display-5 fw-bold mb-2">Featured Properties</h2>
                <p class="text-muted">Handpicked accommodations for an unforgettable experience</p>
            </div>
            <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="btn btn-outline-primary btn-lg rounded-pill">
                View All <i class='bx bx-arrow-right ms-2'></i>
            </a>
        </div>
        <div class="row g-4">
            @php
                $featuredProperties = \App\Models\Property::featured()
                    ->active()
                    ->verified()
                    ->with(['type', 'activeRooms', 'reviews'])
                    ->take(6)
                    ->get();
            @endphp
            @forelse($featuredProperties as $property)
            <div class="col-lg-4 col-md-6">
                <div class="property-card border-0 shadow-sm rounded-4 overflow-hidden h-100 hover-lift">
                    <div class="property-image position-relative" style="height: 250px; overflow: hidden;">
                        <img src="{{ $property->cover_image_url }}" alt="{{ $property->name }}" 
                             class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s;">
                        @if($property->isVerified())
                        <span class="badge bg-success position-absolute top-0 end-0 m-3">
                            <i class='bx bx-check-circle me-1'></i>Verified
                        </span>
                        @endif
                        @if($property->host && $property->host->hostProfile && $property->host->hostProfile->is_superhost)
                        <span class="badge bg-purple position-absolute top-0 start-0 m-3">
                            ⭐ Superhost
                        </span>
                        @endif
                        <div class="property-overlay position-absolute bottom-0 start-0 end-0 p-3 text-white" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                            <h5 class="mb-0 fw-bold">{{ $property->name }}</h5>
                            <p class="mb-0 small">
                                <i class='bx bx-map'></i> {{ $property->city }}, {{ $property->state ?? $property->country }}
                            </p>
                        </div>
                    </div>
                    <div class="property-content p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="badge {{ $property->listing_type === 'hotel' ? 'bg-primary' : 'bg-success' }} mb-2">
                                    {{ $property->listing_type === 'hotel' ? 'Hotel' : 'Unique Stay' }}
                                </span>
                                @if($property->average_rating || $property->reviews->count() > 0)
                                <div class="d-flex align-items-center gap-1">
                                    <i class='bx bxs-star text-warning'></i>
                                    <span class="fw-bold">{{ number_format($property->average_rating ?? $property->reviews->avg('overall_rating') ?? 0, 1) }}</span>
                                    <span class="text-muted small">({{ $property->reviews->count() }})</span>
                                </div>
                                @endif
                            </div>
                            @if($property->activeRooms->count() > 0)
                            <div class="text-end">
                                <h5 class="mb-0 text-primary fw-bold">₹{{ number_format($property->activeRooms->min('price')) }}</h5>
                                <small class="text-muted">/night</small>
                            </div>
                            @endif
                        </div>
                        <p class="text-muted mb-3 small">{{ \Illuminate\Support\Str::limit($property->description ?? 'Beautiful property in a great location.', 100) }}</p>
                        <a href="{{ route('property.show', $property->id) }}" class="btn btn-primary w-100 rounded-pill">
                            View Details <i class='bx bx-arrow-right ms-2'></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class='bx bx-building-house text-muted' style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">No featured properties yet</h4>
                <p class="text-muted">Check back soon for amazing properties!</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Become a Host CTA -->
<section class="become-host-section py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold text-white mb-3">Become a Host</h2>
                <p class="lead text-white mb-4" style="opacity: 0.95;">
                    List your property and start earning. Whether you're a hotel owner or have a unique space to share, 
                    join thousands of successful hosts on our platform.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('property.create') }}" class="btn btn-light btn-lg px-5 rounded-pill fw-bold">
                        <i class='bx bx-plus-circle me-2'></i>List Your Property
                    </a>
                    <a href="{{ route('how-it-works-host') }}" class="btn btn-outline-light btn-lg px-5 rounded-pill fw-bold">
                        Learn More <i class='bx bx-arrow-right ms-2'></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <div class="host-stats bg-white bg-opacity-10 rounded-4 p-4 text-white">
                    <h3 class="display-4 fw-bold mb-2">10K+</h3>
                    <p class="mb-0">Active Hosts</p>
                    <hr class="my-3" style="border-color: rgba(255,255,255,0.3);">
                    <h3 class="display-4 fw-bold mb-2">50K+</h3>
                    <p class="mb-0">Properties Listed</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
@include('frontend.home.testimonials')

<!-- FAQ Section -->
@include('frontend.home.faq')

<!-- Blog Section -->
@include('frontend.home.blog')

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.property-card:hover .property-image img {
    transform: scale(1.1);
}

.bg-purple {
    background-color: #6f42c1 !important;
}
</style>

@endsection
