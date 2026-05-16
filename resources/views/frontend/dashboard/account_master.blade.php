@extends('frontend.main_master')

@section('main')
<div class="inner-banner inner-bg6">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li>
                    <a href="{{ url('/') }}">{{ __('site.nav.home') }}</a>
                </li>
                <li><i class="bx bx-chevron-right" aria-hidden="true"></i></li>
                @hasSection('account_breadcrumb')
                    @yield('account_breadcrumb')
                    <li><i class="bx bx-chevron-right" aria-hidden="true"></i></li>
                @endif
                <li>@yield('account_title')</li>
            </ul>
            <h3>@yield('account_title')</h3>
        </div>
    </div>
</div>

<div class="service-details-area pt-100 pb-70 nx-account-area nx-premium-account-shell">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                @include('frontend.dashboard.user_menu')
            </div>
            <div class="col-lg-9">
                <div class="service-article nx-account-panel">
                    @yield('account_content')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
