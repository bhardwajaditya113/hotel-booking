@extends('frontend.main_master')
@section('main')

<!-- Inner Banner -->
<div class="inner-banner inner-bg1">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>About Us</li>
            </ul>
            <h3>About Us</h3>
        </div>
    </div>
</div>
<!-- Inner Banner End -->

<!-- About Area -->
<div class="about-area pt-100 pb-70">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="about-content">
                    <div class="section-title">
                        <span class="sp-color">About Our Platform</span>
                        <h2>Welcome to the Future of Travel</h2>
                        <p>
                            We're revolutionizing the way people find and book accommodations. Our hybrid platform combines 
                            the best of hotel aggregation (OYO style) and unique stays (Airbnb style), offering travelers 
                            an unparalleled choice of verified properties. Whether you're looking for a luxury hotel or a 
                            cozy apartment, we've got you covered.
                        </p>
                        <p>
                            For hosts, we provide powerful tools to manage properties, reach more guests, and grow your 
                            business. Join thousands of successful hosts and hotels already on our platform.
                        </p>
                    </div>
                    <div class="about-content-card d-flex">
                        <div class="content-card-icon">
                            <i class='flaticon-quality'></i>
                        </div>
                        <div class="about-card-content">
                            <h3>Premium Quality</h3>
                            <p>We maintain the highest standards of quality in all our services and amenities.</p>
                        </div>
                    </div>
                    <div class="about-content-card d-flex">
                        <div class="content-card-icon">
                            <i class='flaticon-customer-service'></i>
                        </div>
                        <div class="about-card-content">
                            <h3>24/7 Support</h3>
                            <p>Our dedicated team is available around the clock to assist you with any needs.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="about-img">
                    <img src="{{ asset('frontend/assets/img/about/about-img1.jpg') }}" alt="About">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- About Area End -->

<!-- Our Team Area -->
<div class="team-area pb-70">
    <div class="container">
        <div class="section-title text-center">
            <span class="sp-color">Our Team</span>
            <h2>Meet Our Dedicated Team</h2>
        </div>
        
        @php
            $teams = App\Models\Team::latest()->limit(4)->get();
        @endphp
        
        @if($teams->count() > 0)
        <div class="row pt-45">
            @foreach($teams as $team)
            <div class="col-lg-3 col-md-6">
                <div class="team-card">
                    <a href="#">
                        <img src="{{ asset($team->image) }}" alt="{{ $team->name }}">
                    </a>
                    <div class="content">
                        <a href="#">
                            <h3>{{ $team->name }}</h3>
                        </a>
                        <span>{{ $team->position }}</span>
                    </div>
                    <ul class="social-link">
                        @if($team->facebook)
                        <li>
                            <a href="{{ $team->facebook }}" target="_blank">
                                <i class='bx bxl-facebook'></i>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <p>Our team information will be updated soon.</p>
        </div>
        @endif
    </div>
</div>
<!-- Team Area End -->

<!-- Why Choose Area -->
<div class="choose-area pt-100 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="choose-content">
                    <div class="section-title">
                        <span class="sp-color">Why Choose Us</span>
                        <h2>We Provide Best Experience</h2>
                        <p>
                            Our commitment to excellence ensures that every guest enjoys a memorable stay. 
                            From luxurious accommodations to exceptional dining experiences.
                        </p>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-6">
                            <div class="choose-content-card">
                                <div class="content">
                                    <i class="flaticon-bed"></i>
                                    <h3>Luxury Rooms</h3>
                                </div>
                                <p>Comfortable and elegantly designed rooms.</p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-6">
                            <div class="choose-content-card">
                                <div class="content">
                                    <i class="flaticon-restaurant"></i>
                                    <h3>Fine Dining</h3>
                                </div>
                                <p>Exquisite cuisine from world-class chefs.</p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-6">
                            <div class="choose-content-card">
                                <div class="content">
                                    <i class="flaticon-spa"></i>
                                    <h3>Spa & Wellness</h3>
                                </div>
                                <p>Relax and rejuvenate at our spa.</p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-6">
                            <div class="choose-content-card">
                                <div class="content">
                                    <i class="flaticon-wifi"></i>
                                    <h3>Free WiFi</h3>
                                </div>
                                <p>Stay connected with high-speed internet.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="choose-img">
                    <img src="{{ asset('frontend/assets/img/about/about-img2.jpg') }}" alt="Images">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Why Choose Area End -->

@endsection
