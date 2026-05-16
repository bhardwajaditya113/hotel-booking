<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), config('locales.rtl', []), true) ? 'rtl' : 'ltr' }}">
    <head>
        <!-- Required Meta Tags -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#f4f6f8">
        <meta name="description" content="{{ __('site.meta_description', ['name' => config('app.name', 'Elapse')]) }}">
        <meta name="application-name" content="{{ config('app.name', 'Elapse') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Outfit:wght@400;500;600;700;800&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">

        <!-- Bootstrap CSS --> 
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
        <!-- Animate Min CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/animate.min.css') }}">
        <!-- Flaticon CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/fonts/flaticon.css') }}">
        <!-- Boxicons CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/boxicons.min.css') }}">
        <!-- Magnific Popup CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/magnific-popup.css') }}">
        <!-- Owl Carousel Min CSS --> 
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/owl.theme.default.min.css') }}">
        <!-- Nice Select Min CSS --> 
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/nice-select.min.css') }}">
        <!-- Meanmenu CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/meanmenu.css') }}">
        <!-- Jquery Ui CSS -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/jquery-ui.css') }}">
        <!-- Legacy layout grid (kept for structure; theme layer loads after) -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/responsive.css') }}">
        <!-- Elapse guest portal theme (loads last) -->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/nexstay-theme.css') }}">
        @vite(array_values(array_filter([
            'resources/css/app.css',
            auth()->check() ? 'resources/js/portal-sync.js' : null,
            auth()->check() ? 'resources/js/booking-realtime.js' : null,
        ])))
        @auth
            <meta name="portal-sync-version" content="{{ \App\Support\PortalSync::version() }}">
            <meta name="portal-sync-poll-url" content="{{ route('portal.sync') }}">
            <meta name="auth-user-id" content="{{ auth()->id() }}">
            <meta name="booking-realtime-msg-host-request" content="{{ e(__('frontend.host_hub.rt_host_request')) }}">
            <meta name="booking-realtime-msg-guest-approved" content="{{ e(__('frontend.host_hub.rt_guest_approved')) }}">
            <meta name="booking-realtime-msg-guest-declined" content="{{ e(__('frontend.host_hub.rt_guest_declined')) }}">
        @endauth
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
        @stack('nexstay-styles')

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('frontend/assets/img/favicon.png') }}">

        	<!-- toastr CSS -->
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" >
    <!-- toastr CSS -->

        <title>{{ config('app.name', 'Elapse') }}</title>
    </head>
    <body class="portal portal-guest nex-future">
        <a class="nx-skip-link visually-hidden-focusable" href="#main-content">{{ __('site.accessibility.skip_to_main') }}</a>

        <!-- PreLoader Start -->
        <div class="preloader">
            <div class="d-table">
                <div class="d-table-cell">
                    <div class="sk-cube-area">
                        <div class="sk-cube1 sk-cube"></div>
                        <div class="sk-cube2 sk-cube"></div>
                        <div class="sk-cube4 sk-cube"></div>
                        <div class="sk-cube3 sk-cube"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- PreLoader End -->

        <!-- Top Header Start -->
        @include('frontend.body.header')
        <!-- Top Header End -->

        <!-- Start Navbar Area -->
        @include('frontend.body.navbar')
        <!-- End Navbar Area -->

        <main id="main-content" class="nx-main" tabindex="-1">
            @yield('main')
        </main>

        <!-- Footer Area -->
        @include('frontend.body.footer')
        <!-- Footer Area End -->


        <!-- Jquery Min JS -->
        <script src="{{ asset('frontend/assets/js/jquery.min.js') }}"></script>
        <!-- Bootstrap Bundle Min JS -->
        <script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>
        <!-- Magnific Popup Min JS -->
        <script src="{{ asset('frontend/assets/js/jquery.magnific-popup.min.js') }}"></script>
        <!-- Owl Carousel Min JS -->
        <script src="{{ asset('frontend/assets/js/owl.carousel.min.js') }}"></script>
        <!-- Nice Select Min JS -->
        <script src="{{ asset('frontend/assets/js/jquery.nice-select.min.js') }}"></script>
        <!-- Wow Min JS -->
        <script src="{{ asset('frontend/assets/js/wow.min.js') }}"></script>
        <!-- Jquery Ui JS -->
        <script src="{{ asset('frontend/assets/js/jquery-ui.js') }}"></script>
        <!-- Meanmenu JS -->
        <script src="{{ asset('frontend/assets/js/meanmenu.js') }}"></script>
        <!-- Ajaxchimp Min JS -->
        <script src="{{ asset('frontend/assets/js/jquery.ajaxchimp.min.js') }}"></script>
        <!-- Form Validator Min JS -->
        <script src="{{ asset('frontend/assets/js/form-validator.min.js') }}"></script>
        <!-- Contact Form JS -->
        <script src="{{ asset('frontend/assets/js/contact-form-script.js') }}"></script>
        <!-- Custom JS -->
        <script src="{{ asset('frontend/assets/js/custom.js') }}"></script>

        <script>
            window.NEXSTAY_MAP_TILE = @json(config('nexstay.maps.tile_url'));
            window.NEXSTAY_MAP_ATTR = @json(config('nexstay.maps.tile_attribution'));
        </script>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="{{ asset('js/nexstay-maps-init.js') }}"></script>
        <script src="{{ asset('js/nexstay-location.js') }}"></script>
        @stack('nexstay-scripts')
        @stack('nexstay-page-scripts')
        @stack('scripts')
        
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
 @if(Session::has('message'))
 var type = "{{ Session::get('alert-type','info') }}"
 switch(type){
    case 'info':
    toastr.info(" {{ Session::get('message') }} ");
    break;

    case 'success':
    toastr.success(" {{ Session::get('message') }} ");
    break;

    case 'warning':
    toastr.warning(" {{ Session::get('message') }} ");
    break;

    case 'error':
    toastr.error(" {{ Session::get('message') }} ");
    break; 
 }
 @endif 
</script>


    </body>
</html>