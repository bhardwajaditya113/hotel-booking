@extends('frontend.main_master')
@section('main')

<!-- Inner Banner -->
<div class="inner-banner inner-bg7">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>Booking Confirmation</li>
            </ul>
            <h3>Booking Confirmation</h3>
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
                    <div class="card-header bg-success text-white text-center py-4">
                        <i class='bx bx-check-circle' style="font-size: 60px;"></i>
                        <h2 class="mt-3 mb-0">Booking Confirmed!</h2>
                        <p class="mb-0">Thank you for your booking</p>
                    </div>
                    <div class="card-body p-5">
                        @if(session('message'))
                        <div class="alert alert-{{ session('alert-type') }} alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <div class="booking-summary">
                            <h4 class="mb-4"><i class='bx bx-receipt'></i> Booking Details</h4>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Booking Reference:</strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="badge bg-primary fs-6">{{ $booking->code }}</span>
                                </div>
                            </div>

                            @if($booking->property)
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Property:</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->property->name }}
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Room Type:</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->room->type->name ?? 'Room' }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Check-in Date:</strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="badge bg-info text-dark">{{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Check-out Date:</strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="badge bg-warning text-dark">{{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Number of Nights:</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->total_night }} night(s)
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Number of Rooms:</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->number_of_rooms }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Number of Guests:</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->persion }}
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3"><i class='bx bx-money'></i> Payment Details</h5>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <strong>Subtotal:</strong>
                                </div>
                                <div class="col-md-6">
                                    ₹{{ number_format($booking->subtotal, 2) }}
                                </div>
                            </div>

                            @if($booking->discount > 0)
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <strong>Discount:</strong>
                                </div>
                                <div class="col-md-6 text-success">
                                    -₹{{ number_format($booking->discount, 2) }}
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Total Amount:</strong>
                                </div>
                                <div class="col-md-6">
                                    <strong class="text-primary fs-5">₹{{ number_format($booking->total_price, 2) }}</strong>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Payment Status:</strong>
                                </div>
                                <div class="col-md-6">
                                    @if($booking->payment_status == 1)
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Payment Method:</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->payment_method }}
                                </div>
                            </div>

                            @if($booking->transation_id)
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Transaction ID:</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">{{ $booking->transation_id }}</small>
                                </div>
                            </div>
                            @endif

                            <hr class="my-4">

                            <h5 class="mb-3"><i class='bx bx-user'></i> Guest Information</h5>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <strong>Name:</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->name }}
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <strong>Email:</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->email }}
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <strong>Phone:</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->phone }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Address:</strong>
                                </div>
                                <div class="col-md-6">
                                    {{ $booking->address ?? 'N/A' }}
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
                            <strong>Important:</strong> A confirmation email has been sent to <strong>{{ $booking->email }}</strong>. 
                            Please check your inbox and keep your booking reference number safe.
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('user.booking') }}" class="btn btn-primary me-2">
                                <i class='bx bx-list-ul'></i> View My Bookings
                            </a>
                            <a href="{{ route('user.invoice', $booking->id) }}" class="btn btn-outline-primary">
                                <i class='bx bx-download'></i> Download Invoice
                            </a>
                            <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                                <i class='bx bx-home'></i> Back to Home
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

