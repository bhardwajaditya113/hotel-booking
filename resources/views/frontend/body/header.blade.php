
@php
    $setting = App\Models\SiteSetting::query()->first();
    $supported = config('locales.supported', ['en']);
    $labels = config('locales.labels', []);
@endphp

<header class="top-header top-header-bg nx-premium-topbar" aria-label="Site shortcuts">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-3 col-md-2 pr-0">
                <div class="language-list">
                    <label for="nx-site-locale" class="visually-hidden">{{ __('site.header.language') }}</label>
                    <select id="nx-site-locale" class="language-list-item" autocomplete="off" onchange="if (this.value) { window.location.href = '{{ url('/locale') }}/' + encodeURIComponent(this.value); }">
                        @foreach ($supported as $code)
                            <option value="{{ $code }}" @selected(app()->getLocale() === $code)>{{ $labels[$code] ?? strtoupper($code) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-9 col-md-10">
                <div class="header-right">
                    <ul>
                        <li>
                            <i class='bx bx-home-alt'></i>
                            <a href="#">{{ $setting?->address ?? '' }}</a>
                        </li>
                        <li>
                            <i class='bx bx-phone-call'></i>
                            <a href="tel:{{ $setting?->phone ?? '' }}">{{ $setting?->phone ?? '' }}</a>
                        </li>

                        @auth

                            <li>
                                <i class='bx bxs-user-pin'></i>
                                <a href="{{ route('dashboard') }}">{{ __('site.header.account') }}</a>
                            </li>

                            <li>
                                <i class='bx bxs-user-rectangle'></i>
                                <a href="{{ route('user.logout') }}">{{ __('site.header.sign_out') }}</a>
                            </li>

                        @else

                            <li>
                                <i class='bx bxs-user-pin'></i>
                                <a href="{{ route('login') }}">{{ __('site.header.sign_in') }}</a>
                            </li>

                            <li>
                                <i class='bx bxs-user-rectangle'></i>
                                <a href="{{ route('register') }}">{{ __('site.header.create_account') }}</a>
                            </li>

                        @endauth

                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
