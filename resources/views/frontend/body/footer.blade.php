@php
    $setting = App\Models\SiteSetting::query()->first();
    $brand = config('app.name', 'Elapse');
@endphp

<footer class="footer-area footer-bg nx-premium-footer">
    <div class="container">
        <div class="footer-top pt-100 pb-70">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <div class="footer-logo">
                            <a href="{{ url('/') }}">
                                @if($setting && $setting->logo)
                                <img src="{{ \App\Support\MediaUrl::resolve($setting->logo, 'upload/logo') }}" class="nx-footer-logo" alt="{{ $brand }}">
                                @else
                                <x-brand-wordmark variant="footer" class="h4 mb-0" />
                                @endif
                            </a>
                        </div>
                        <p>
                            {{ __('site.footer.tagline') }}
                        </p>
                        <ul class="footer-list-contact">
                            <li>
                                <i class='bx bx-home-alt'></i>
                                <a href="#">{{ $setting?->address ?? __('site.footer.hotel_address') }}</a>
                            </li>
                            <li>
                                <i class='bx bx-phone-call'></i>
                                <a href="tel:{{ $setting?->phone ?? '' }}">{{ $setting?->phone ?? __('site.footer.contact_us') }}</a>
                            </li>
                            <li>
                                <i class='bx bx-envelope'></i>
                                <a href="mailto:{{ $setting?->email ?? '' }}">{{ $setting?->email ?? __('site.footer.email_us') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget pl-5">
                        <h3>{{ __('site.footer.for_guests') }}</h3>
                        <ul class="footer-list">
                            <li>
                                <a href="{{ route('search.results', ['search_mode' => 'properties']) }}">
                                    <i class='bx bx-caret-right'></i>
                                    {{ __('site.footer.search_properties') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('how-it-works') }}">
                                    <i class='bx bx-caret-right'></i>
                                    {{ __('site.footer.how_it_works') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('features') }}">
                                    <i class='bx bx-caret-right'></i>
                                    {{ __('site.footer.features') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pricing') }}">
                                    <i class='bx bx-caret-right'></i>
                                    {{ __('site.footer.pricing') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('blog.list') }}">
                                    <i class='bx bx-caret-right'></i>
                                    {{ __('site.footer.travel_journal') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('search.map') }}">
                                    <i class='bx bx-caret-right'></i>
                                    {{ __('site.footer.explore_map') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h3>{{ __('site.footer.for_hosts') }}</h3>
                        <ul class="footer-list">
                            <li>
                                <a href="{{ route('property.create') }}">
                                    <i class='bx bx-caret-right'></i>
                                    {{ __('site.footer.list_property') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('how-it-works-host') }}">
                                    <i class='bx bx-caret-right'></i>
                                    {{ __('site.footer.become_host') }}
                                </a>
                            </li>
                            @auth
                            <li>
                                <a href="{{ route('property.dashboard') }}">
                                    <i class='bx bx-caret-right'></i>
                                    {{ __('site.footer.host_dashboard') }}
                                </a>
                            </li>
                            @else
                            <li>
                                <a href="{{ route('register') }}">
                                    <i class='bx bx-caret-right'></i>
                                    {{ __('site.footer.sign_up') }}
                                </a>
                            </li>
                            @endauth
                            @auth
                            <li>
                                <a href="{{ route('dashboard') }}">
                                    <i class='bx bx-caret-right'></i>
                                    {{ __('site.footer.my_dashboard') }}
                                </a>
                            </li>
                            @endauth
                            <li>
                                <a href="{{ route('show.gallery') }}">
                                    <i class='bx bx-caret-right'></i>
                                    {{ __('site.footer.gallery') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contact.us') }}">
                                    <i class='bx bx-caret-right'></i>
                                    {{ __('site.footer.contact_us_link') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h3>{{ __('site.footer.newsletter_title') }}</h3>
                        <p>
                            {{ __('site.footer.newsletter_desc') }}
                        </p>
                        <div class="footer-form">
                            <form class="newsletter-form" data-toggle="validator" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <input type="email" class="form-control" placeholder="{{ __('site.footer.email_placeholder') }}" name="email" required autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12">
                                        <button type="submit" class="default-btn btn-bg-one">
                                            {{ __('site.footer.subscribe_now') }}
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
                            {{ $setting?->copyright ?? __('site.footer.copyright_fallback', ['year' => date('Y'), 'name' => $brand]) }}
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
