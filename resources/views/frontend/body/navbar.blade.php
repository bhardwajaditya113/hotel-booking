@php
    $setting = App\Models\SiteSetting::query()->first();
    $brand = config('app.name', 'Elapse');
@endphp

<div class="navbar-area">
    <!-- Menu For Mobile Device -->
    <div class="mobile-nav">
        <a href="{{ url('/') }}" class="logo" aria-label="{{ $brand }} — {{ __('site.nav.home') }}">
            @if($setting && $setting->logo)
            <img src="{{ \App\Support\MediaUrl::resolve($setting->logo, 'upload/logo') }}" class="logo-one" alt="{{ $brand }}">
            <img src="{{ \App\Support\MediaUrl::resolve($setting->logo, 'upload/logo') }}" class="logo-two" alt="">
            @else
            <x-brand-wordmark class="h5 mb-0" />
            @endif
        </a>
    </div>

    <!-- Menu For Desktop Device -->
    <div class="main-nav">
        <div class="container">
            <nav class="navbar navbar-expand-md navbar-light" aria-label="Primary">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}" aria-label="{{ $brand }} — {{ __('site.nav.home') }}">
                    @if($setting && $setting->logo)
                    <img src="{{ \App\Support\MediaUrl::resolve($setting->logo, 'upload/logo') }}" class="logo-one" alt="{{ $brand }}">
                    <img src="{{ \App\Support\MediaUrl::resolve($setting->logo, 'upload/logo') }}" class="logo-two" alt="">
                    @else
                    <x-brand-wordmark class="h4 mb-0" />
                    @endif
                </a>

                <div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">
                    <ul class="navbar-nav m-auto">
                        <li class="nav-item">
                            <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}" @if(request()->is('/')) aria-current="page" @endif>
                                {{ __('site.nav.home') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="nav-link {{ request()->is('search*') || request()->is('properties*') ? 'active' : '' }}" @if(request()->is('search*') || request()->is('properties*')) aria-current="page" @endif>
                                {{ __('site.nav.stays') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('search.map') }}" class="nav-link {{ request()->is('search/map') ? 'active' : '' }}" @if(request()->is('search/map')) aria-current="page" @endif>
                                {{ __('site.nav.map') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('froom.all') }}" class="nav-link {{ request()->is('rooms*') || request()->is('room/*') ? 'active' : '' }}" @if(request()->is('rooms*') || request()->is('room/*')) aria-current="page" @endif>
                                {{ __('site.nav.rooms') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('how-it-works') }}" class="nav-link {{ request()->is('how-it-works*') ? 'active' : '' }}" @if(request()->is('how-it-works*')) aria-current="page" @endif>
                                {{ __('site.nav.how_it_works') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/about') }}" class="nav-link {{ request()->is('about') ? 'active' : '' }}" @if(request()->is('about')) aria-current="page" @endif>
                                {{ __('site.nav.about') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('show.gallery') }}" class="nav-link {{ request()->is('gallery') ? 'active' : '' }}" @if(request()->is('gallery')) aria-current="page" @endif>
                                {{ __('site.nav.gallery') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('blog.list') }}" class="nav-link {{ request()->is('blog*') ? 'active' : '' }}" @if(request()->is('blog*')) aria-current="page" @endif>
                                {{ __('site.nav.blog') }}
                            </a>
                        </li>
                        @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->is('dashboard') || request()->is('messages*') || request()->is('wishlists*') || request()->is('property*') ? 'active' : '' }}" href="#" id="nxAccountMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
                                {{ __('site.nav.account_menu') }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="nxAccountMenu">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">{{ __('site.nav.overview') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.booking') }}">{{ __('site.nav.my_bookings') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('wishlists.index') }}">{{ __('site.nav.wishlists') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('messages.index') }}">{{ __('site.nav.messages') }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('property.dashboard') }}">{{ __('site.nav.host_hub') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('property.create') }}">{{ __('site.nav.list_new_stay') }}</a></li>
                            </ul>
                        </li>
                        @endauth
                        <li class="nav-item">
                            <a href="{{ route('contact.us') }}" class="nav-link {{ request()->is('contact') ? 'active' : '' }}" @if(request()->is('contact')) aria-current="page" @endif>
                                {{ __('site.nav.help') }}
                            </a>
                        </li>
                    </ul>

                    <div class="nav-btn d-flex flex-wrap gap-2 align-items-center justify-content-end">
                        @auth
                        <a href="{{ route('property.create') }}" class="default-btn btn-bg-two border-radius-5">{{ __('site.nav.list_stay') }}</a>
                        @else
                        <a href="{{ route('register') }}" class="default-btn btn-bg-two border-radius-5">{{ __('site.header.create_account') }}</a>
                        @endauth
                        <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="default-btn btn-bg-one border-radius-5">{{ __('site.nav.find_stay') }}</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>
