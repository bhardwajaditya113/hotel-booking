@php
    $setting = App\Models\SiteSetting::find(1);
@endphp


<div class="navbar-area">
    <!-- Menu For Mobile Device -->
    <div class="mobile-nav">
        <a href="{{ url('/') }}" class="logo">
            @if($setting && $setting->logo)
            <img src="{{ asset($setting->logo) }}" class="logo-one" alt="Logo">
            <img src="{{ asset($setting->logo) }}" class="logo-two" alt="Logo">
            @else
            <span class="text-white fw-bold">Hotel</span>
            @endif
        </a>
    </div>

    <!-- Menu For Desktop Device -->
    <div class="main-nav">
        <div class="container">
            <nav class="navbar navbar-expand-md navbar-light ">
                <a class="navbar-brand" href="{{ url('/') }}">
                    @if($setting && $setting->logo)
                    <img src="{{ asset($setting->logo) }}" class="logo-one" alt="Logo">
                    <img src="{{ asset($setting->logo) }}" class="logo-two" alt="Logo">
                    @else
                    <span class="text-dark fw-bold h4">Hotel</span>
                    @endif
                </a>

                <div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">
                    <ul class="navbar-nav m-auto">
                        <li class="nav-item">
                            <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                                Home  
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="nav-link {{ request()->is('search*') || request()->is('properties*') ? 'active' : '' }}">
                                Properties
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('how-it-works') }}" class="nav-link {{ request()->is('how-it-works*') ? 'active' : '' }}">
                                How It Works
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/about') }}" class="nav-link {{ request()->is('about') ? 'active' : '' }}">
                                About
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('show.gallery') }}" class="nav-link {{ request()->is('gallery') ? 'active' : '' }}">
                              Gallery
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('blog.list') }}" class="nav-link {{ request()->is('blog*') ? 'active' : '' }}">
                                Blog 
                            </a>
                        </li>
                        @auth
                        <li class="nav-item">
                            <a href="{{ route('property.dashboard') }}" class="nav-link {{ request()->is('property*') ? 'active' : '' }}">
                                Host Dashboard
                            </a>
                        </li>
                        @endauth
    @php
        $rooms = App\Models\Room::with('type')->latest()->get();
    @endphp
                        <li class="nav-item">
                            <a href="{{ route('froom.all') }}" class="nav-link {{ request()->is('rooms*') ? 'active' : '' }}">
                                All Rooms
                                @if($rooms->count() > 0)
                                <i class='bx bx-chevron-down'></i>
                                @endif
                            </a>
                            @if($rooms->count() > 0)
                            <ul class="dropdown-menu">
                                @foreach ($rooms as $item) 
                                <li class="nav-item">
                                    <a href="{{ url('/room/details/'.$item->id) }}" class="nav-link">
                                        {{ $item->type->name ?? $item->room_name ?? 'Room' }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('contact.us') }}" class="nav-link {{ request()->is('contact') ? 'active' : '' }}">
                                Contact
                            </a>
                        </li>

                        <li class="nav-item-btn">
                            <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="default-btn btn-bg-one border-radius-5">Book Now</a>
                        </li>
                    </ul>

                    <div class="nav-btn d-flex gap-2">
                        @auth
                        <a href="{{ route('property.create') }}" class="default-btn btn-bg-two border-radius-5">Become Host</a>
                        @else
                        <a href="{{ route('register') }}" class="default-btn btn-bg-two border-radius-5">Sign Up</a>
                        @endauth
                        <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="default-btn btn-bg-one border-radius-5">Book Now</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>