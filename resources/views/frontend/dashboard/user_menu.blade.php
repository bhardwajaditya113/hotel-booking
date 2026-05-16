@php
$id = Auth::user()->id;
$profileData = App\Models\User::find($id);
@endphp

<aside class="service-side-bar" aria-label="Account sidebar">
    <div class="services-bar-widget">
        <h3 class="title">{{ __('frontend.account.sidebar_title') }}</h3>
        <div class="side-bar-categories">
            <div class="nx-account-profile-card pt-3 px-2">
                <img src="{{ (!empty($profileData->photo)) ? url('upload/user_images/'.$profileData->photo) : url('upload/no_image.jpg') }}" class="rounded-circle mx-auto d-block nx-account-avatar" alt="">
                <div class="text-center mt-3 mb-3">
                    <div class="fw-semibold">{{ $profileData->name }}</div>
                    <div class="small nx-account-email">{{ $profileData->email }}</div>
                </div>
            </div>
            <nav aria-label="Account pages">
                <ul class="list-unstyled mb-0 nx-account-links">
                    <li><a href="{{ route('search.results', ['search_mode' => 'properties']) }}">{{ __('site.nav.find_stay') }}</a></li>
                    <li><a href="{{ route('user.booking') }}">{{ __('site.nav.my_bookings') }}</a></li>
                    <li><a href="{{ route('wishlists.index') }}">{{ __('site.nav.wishlists') }}</a></li>
                    <li><a href="{{ route('messages.index') }}">{{ __('site.nav.messages') }}</a></li>
                    <li><a href="{{ route('property.dashboard') }}">{{ __('site.nav.host_hub') }}</a></li>
                    <li><a href="{{ route('dashboard') }}">{{ __('site.nav.overview') }}</a></li>
                    <li><a href="{{ route('wallet.index') }}">{{ __('frontend.account.wallet') }}</a></li>
                    <li><a href="{{ route('loyalty.index') }}">{{ __('frontend.account.loyalty') }}</a></li>
                    <li><a href="{{ route('notifications.index') }}">{{ __('frontend.account.notifications') }}</a></li>
                    <li><a href="{{ route('reviews.mine') }}">{{ __('frontend.account.my_reviews') }}</a></li>
                    <li><a href="{{ route('user.profile') }}">{{ __('frontend.account.profile_settings') }}</a></li>
                    <li><a href="{{ route('user.change.password') }}">{{ __('frontend.account.password') }}</a></li>
                    <li class="pt-2 mt-2 border-top border-secondary border-opacity-25"><a href="{{ route('user.logout') }}">{{ __('frontend.account.log_out') }}</a></li>
                </ul>
            </nav>
        </div>
    </div>
</aside>
