@php
    $setting = App\Models\SiteSetting::find(1);
@endphp

<footer class="footer-area footer-bg">
    <div class="container">
        <div class="footer-top pt-100 pb-70">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <div class="footer-logo">
                            <a href="{{ url('/') }}">
                                @if($setting && $setting->logo)
                                <img src="{{ asset($setting->logo) }}" alt="Images">
                                @else
                                <span class="text-white fw-bold h4">Hotel</span>
                                @endif
                            </a>
                        </div>
                        <p>
                            Experience luxury and comfort at our hotel. We provide world-class amenities and exceptional service for an unforgettable stay.
                        </p>
                        <ul class="footer-list-contact">
                            <li>
                                <i class='bx bx-home-alt'></i>
                                <a href="#">{{ $setting->address ?? 'Hotel Address' }}</a>
                            </li>
                            <li>
                                <i class='bx bx-phone-call'></i>
                                <a href="tel:{{ $setting->phone ?? '' }}">{{ $setting->phone ?? 'Contact Us' }}</a>
                            </li>
                            <li>
                                <i class='bx bx-envelope'></i>
                                <a href="mailto:{{ $setting->email ?? '' }}">{{ $setting->email ?? 'Email Us' }}</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget pl-5">
                        <h3>For Guests</h3>
                        <ul class="footer-list">
                            <li>
                                <a href="{{ route('search.results', ['search_mode' => 'properties']) }}">
                                    <i class='bx bx-caret-right'></i>
                                    Search Properties
                                </a>
                            </li> 
                            <li>
                                <a href="{{ route('how-it-works') }}">
                                    <i class='bx bx-caret-right'></i>
                                    How It Works
                                </a>
                            </li> 
                            <li>
                                <a href="{{ route('features') }}">
                                    <i class='bx bx-caret-right'></i>
                                    Features
                                </a>
                            </li> 
                            <li>
                                <a href="{{ route('pricing') }}">
                                    <i class='bx bx-caret-right'></i>
                                    Pricing
                                </a>
                            </li> 
                            <li>
                                <a href="{{ route('froom.all') }}">
                                    <i class='bx bx-caret-right'></i>
                                    All Rooms
                                </a>
                            </li> 
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h3>For Hosts</h3>
                        <ul class="footer-list">
                            <li>
                                <a href="{{ route('property.create') }}">
                                    <i class='bx bx-caret-right'></i>
                                    List Your Property
                                </a>
                            </li> 
                            <li>
                                <a href="{{ route('how-it-works-host') }}">
                                    <i class='bx bx-caret-right'></i>
                                    Become a Host
                                </a>
                            </li> 
                            @auth
                            <li>
                                <a href="{{ route('property.dashboard') }}">
                                    <i class='bx bx-caret-right'></i>
                                    Host Dashboard
                                </a>
                            </li>
                            @else
                            <li>
                                <a href="{{ route('register') }}">
                                    <i class='bx bx-caret-right'></i>
                                    Sign Up
                                </a>
                            </li>
                            @endauth 
                            @auth
                            <li>
                                <a href="{{ route('dashboard') }}">
                                    <i class='bx bx-caret-right'></i>
                                    My Dashboard
                                </a>
                            </li> 
                            @endauth
                            <li>
                                <a href="{{ route('show.gallery') }}">
                                    <i class='bx bx-caret-right'></i>
                                    Gallery
                                </a>
                            </li> 
                            <li>
                                <a href="{{ route('contact.us') }}">
                                    <i class='bx bx-caret-right'></i>
                                    Contact Us
                                </a>
                            </li> 
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h3>Newsletter</h3>
                        <p>
                            Subscribe to our newsletter to receive the latest updates, offers, and travel tips directly in your inbox.
                        </p>
                        <div class="footer-form">
                            <form class="newsletter-form" data-toggle="validator" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <input type="email" class="form-control" placeholder="Your Email*" name="email" required autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12">
                                        <button type="submit" class="default-btn btn-bg-one">
                                            Subscribe Now
                                        </button>
                                        <div id="validator-newsletter" class="form-result"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="copy-right-area">
            <div class="row">
                <div class="col-lg-8 col-md-8">
                    <div class="copy-right-text text-align1">
                        <p>
                            {{ $setting->copyright ?? '© ' . date('Y') . ' Hotel. All Rights Reserved.' }}
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4">
                    <div class="social-icon text-align2">
                        <ul class="social-link">
                            @if($setting && $setting->facebook)
                            <li>
                                <a href="{{ $setting->facebook }}" target="_blank"><i class='bx bxl-facebook'></i></a>
                            </li>
                            @endif 
                            @if($setting && $setting->twitter)
                            <li>
                                <a href="{{ $setting->twitter }}" target="_blank"><i class='bx bxl-twitter'></i></a>
                            </li>
                            @endif 
                            <li>
                                <a href="#" target="_blank"><i class='bx bxl-instagram'></i></a>
                            </li> 
                            <li>
                                <a href="#" target="_blank"><i class='bx bxl-youtube'></i></a>
                            </li> 
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
</footer>
