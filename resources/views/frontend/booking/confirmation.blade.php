@extends('frontend.main_master')
@section('main')

<!-- Inner Banner -->
<div class="inner-banner inner-bg7">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li>
                    <a href="{{ url('/') }}">{{ __('site.nav.home') }}</a>
                </li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>{{ __('frontend.booking.confirmation_breadcrumb') }}</li>
            </ul>
            <h3>{{ __('frontend.booking.confirmation_title') }}</h3>
        </div>
    </div>
</div>
<!-- Inner Banner End -->

<!-- Booking Confirmation Area -->
<section class="checkout-area pt-100 pb-70">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    @php
                        $pendingHost = ($booking->host_approval_status ?? null) === 'pending';
                        $declinedHost = ($booking->host_approval_status ?? null) === 'declined';
                        $approvedAwaitPay = ($booking->host_approval_status ?? null) === 'approved'
                            && $booking->payment_method === 'Razorpay'
                            && (int) $booking->payment_status === 0;
                    @endphp
                    <div class="card-header {{ $declinedHost ? 'bg-danger text-white' : ($pendingHost ? 'bg-warning text-dark' : 'bg-success text-white') }} text-center py-4">
                        <i class='bx {{ $declinedHost ? 'bx-x-circle' : ($pendingHost ? 'bx-time-five' : 'bx-check-circle') }}' style="font-size: 60px;"></i>
                        <h2 class="mt-3 mb-0">{{ $declinedHost ? __('frontend.booking.declined_heading') : ($pendingHost ? __('frontend.booking.pending_host_heading') : __('frontend.booking.confirmed_heading')) }}</h2>
                        <p class="mb-0">{{ $declinedHost ? __('frontend.booking.declined_sub') : ($pendingHost ? __('frontend.booking.pending_host_sub') : __('frontend.booking.confirmed_sub')) }}</p>
                    </div>
                    <div class="card-body p-5">
                        @if(session('message'))
                        <div class="alert alert-{{ session('alert-type') }} alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        @if(! empty($approvedAwaitPay))
                        <div class="alert alert-primary rounded-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div>{{ __('frontend.booking.pay_now_intro') }}</div>
                            <a href="{{ route('razorpay.payment', ['booking_id' => $booking->id]) }}" class="btn btn-dark rounded-pill px-4 fw-semibold">{{ __('frontend.booking.pay_now_cta') }}</a>
                        </div>
                        @endif

                        @if($declinedHost && ! empty(trim((string) ($booking->host_decline_notes ?? ''))))
                        <div class="alert alert-light border rounded-4">
                            <strong class="d-block mb-1">{{ __('frontend.booking.host_message_label') }}</strong>
                            <p class="mb-0">{{ $booking->host_decline_notes }}</p>
                        </div>
                        @endif

                        <div class="booking-summary">
                            <h4 class="mb-4"><i class='bx bx-receipt'></i> {{ __('frontend.booking.details_heading') }}</h4>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.booking_reference') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="badge bg-primary fs-6">{{ $booking->code }}</span>
                                </div>
                            </div>

                            @if($booking->property)
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.property') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->property->name }}
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.room_type') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->room->type->name ?? __('frontend.booking.room_fallback') }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.check_in') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="badge bg-info text-dark">{{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.check_out') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="badge bg-warning text-dark">{{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.number_of_nights') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->total_night }} {{ $booking->total_night == 1 ? __('frontend.common.night') : __('frontend.common.nights') }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.number_of_rooms') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->number_of_rooms }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.number_of_guests') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->persion }}
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3"><i class='bx bx-money'></i> {{ __('frontend.booking.payment_details') }}</h5>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.subtotal') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    ₹{{ number_format($booking->subtotal, 2) }}
                                </div>
                            </div>

                            @if($booking->discount > 0)
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.discount') }}</strong>
                                </div>
                                <div class="col-md-6 text-success">
                                    -₹{{ number_format($booking->discount, 2) }}
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.total_amount') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    <strong class="text-primary fs-5">₹{{ number_format($booking->total_price, 2) }}</strong>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.payment_status') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    @if($booking->payment_status == 1)
                                        <span class="badge bg-success">{{ __('frontend.booking.paid') }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ __('frontend.booking.pending') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.payment_method') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->payment_method }}
                                </div>
                            </div>

                            @if($booking->transation_id)
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.transaction_id') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">{{ $booking->transation_id }}</small>
                                </div>
                            </div>
                            @endif

                            <hr class="my-4">

                            <h5 class="mb-3"><i class='bx bx-user'></i> {{ __('frontend.booking.guest_information') }}</h5>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.name') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->name }}
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.email') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->email }}
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.phone') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->phone }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('frontend.booking.address') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->address ?? __('frontend.common.not_available') }}
                                    @if($booking->state || $booking->country || $booking->zip_code)
                                        <br>
                                        @if($booking->state){{ $booking->state }}, @endif
                                        @if($booking->country){{ $booking->country }}@endif
                                        @if($booking->zip_code) - {{ $booking->zip_code }}@endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4">
                            <i class='bx bx-info-circle'></i>
                            <strong>{{ __('frontend.booking.important_note') }}</strong>
                            {{ __('frontend.booking.email_sent_intro') }}
                            <strong>{{ $booking->email }}</strong>.
                            {{ __('frontend.booking.email_sent_outro') }}
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('user.booking') }}" class="btn btn-primary me-2">
                                <i class='bx bx-list-ul'></i> {{ __('frontend.booking.view_my_bookings') }}
                            </a>
                            <a href="{{ route('user.invoice', $booking->id) }}" class="btn btn-outline-primary">
                                <i class='bx bx-download'></i> {{ __('frontend.booking.download_invoice') }}
                            </a>
                            <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                                <i class='bx bx-home'></i> {{ __('frontend.booking.back_to_home') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Booking Confirmation Area End -->

@endsection
