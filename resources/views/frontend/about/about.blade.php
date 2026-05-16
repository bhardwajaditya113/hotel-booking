@extends('frontend.main_master')
@section('main')

<!-- Inner Banner -->
<div class="inner-banner inner-bg1">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li>
                    <a href="{{ url('/') }}">{{ __('site.nav.home') }}</a>
                </li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>{{ __('frontend.about.breadcrumb') }}</li>
            </ul>
            <h3>{{ __('frontend.about.page_title') }}</h3>
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
                        <span class="sp-color">{{ __('frontend.about.platform_eyebrow') }}</span>
                        <h2>{{ __('frontend.about.welcome_heading') }}</h2>
                        <p>{{ __('frontend.about.welcome_p1') }}</p>
                        <p>{{ __('frontend.about.welcome_p2') }}</p>
                    </div>
                    <div class="about-content-card d-flex">
                        <div class="content-card-icon">
                            <i class='flaticon-quality'></i>
                        </div>
                        <div class="about-card-content">
                            <h3>{{ __('frontend.about.premium_title') }}</h3>
                            <p>{{ __('frontend.about.premium_text') }}</p>
                        </div>
                    </div>
                    <div class="about-content-card d-flex">
                        <div class="content-card-icon">
                            <i class='flaticon-customer-service'></i>
                        </div>
                        <div class="about-card-content">
                            <h3>{{ __('frontend.about.support_title') }}</h3>
                            <p>{{ __('frontend.about.support_text') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="about-img">
                    <img src="{{ asset('frontend/assets/img/about/about-img1.jpg') }}" alt="{{ __('frontend.about.img_alt_about') }}">
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
            <span class="sp-color">{{ __('frontend.about.team_eyebrow') }}</span>
            <h2>{{ __('frontend.about.team_heading') }}</h2>
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
                        <img src="{{ \App\Support\MediaUrl::resolve($team->image, 'upload/team') }}" alt="{{ $team->name }}">
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
            <p>{{ __('frontend.about.team_empty') }}</p>
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
                        <span class="sp-color">{{ __('frontend.about.choose_eyebrow') }}</span>
                        <h2>{{ __('frontend.about.choose_heading') }}</h2>
                        <p>{{ __('frontend.about.choose_lead') }}</p>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-6">
                            <div class="choose-content-card">
                                <div class="content">
                                    <i class="flaticon-bed"></i>
                                    <h3>{{ __('frontend.about.luxury_rooms_title') }}</h3>
                                </div>
                                <p>{{ __('frontend.about.luxury_rooms_text') }}</p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-6">
                            <div class="choose-content-card">
                                <div class="content">
                                    <i class="flaticon-restaurant"></i>
                                    <h3>{{ __('frontend.about.fine_dining_title') }}</h3>
                                </div>
                                <p>{{ __('frontend.about.fine_dining_text') }}</p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-6">
                            <div class="choose-content-card">
                                <div class="content">
                                    <i class="flaticon-spa"></i>
                                    <h3>{{ __('frontend.about.spa_title') }}</h3>
                                </div>
                                <p>{{ __('frontend.about.spa_text') }}</p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-6">
                            <div class="choose-content-card">
                                <div class="content">
                                    <i class="flaticon-wifi"></i>
                                    <h3>{{ __('frontend.about.wifi_title') }}</h3>
                                </div>
                                <p>{{ __('frontend.about.wifi_text') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="choose-img">
                    <img src="{{ asset('frontend/assets/img/about/about-img2.jpg') }}" alt="{{ __('frontend.about.choose_img_alt') }}">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Why Choose Area End -->

@endsection
