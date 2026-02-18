@extends('frontend.main_master')
@section('main')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<!-- Inner Banner -->
<div class="inner-banner inner-bg7">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>Payment</li>
            </ul>
            <h3>Complete Payment</h3>
        </div>
    </div>
</div>
<!-- Inner Banner End -->

<!-- Payment Area -->
<section class="checkout-area pt-100 pb-70">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class='bx bx-credit-card'></i> Payment Details</h4>
                    </div>
                    <div class="card-body">
                        <!-- Booking Summary -->
                        <div class="booking-summary mb-4">
                            <h5>Booking Summary</h5>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Room:</span>
                                <strong>{{ $booking->room->type->name ?? 'Room' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Check-in:</span>
                                <strong>{{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Check-out:</span>
                                <strong>{{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Nights:</span>
                                <strong>{{ $booking->total_night }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Rooms:</span>
                                <strong>{{ $booking->number_of_rooms }}</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>${{ number_format($booking->subtotal, 2) }}</span>
                            </div>
                            @if($booking->discount > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Discount:</span>
                                <span>-${{ number_format($booking->discount, 2) }}</span>
                            </div>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total (USD):</strong>
                                <strong class="text-primary">${{ number_format($booking->total_price, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <strong>Total (INR):</strong>
                                <strong class="text-success">₹{{ number_format($booking->total_price * 83, 2) }}</strong>
                            </div>
                            <small class="text-muted">* Exchange rate: 1 USD = 83 INR (approximate)</small>
                        </div>
                        
                        <!-- Razorpay Button -->
                        <div class="text-center">
                            <button id="rzp-button" class="default-btn btn-bg-one border-radius-5 w-100 py-3">
                                <i class='bx bx-lock-alt'></i> Pay with Razorpay
                            </button>
                            
                            @if(!app()->environment('production'))
                            <!-- Test Payment Button (Development Only) -->
                            <form action="{{ route('razorpay.test.payment') }}" method="POST" class="mt-3">
                                @csrf
                                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                <button type="submit" class="btn btn-success w-100 py-2">
                                    <i class='bx bx-check-circle'></i> Test Payment (Skip Razorpay)
                                </button>
                                <small class="text-muted d-block mt-2">Development mode: Complete payment without Razorpay</small>
                            </form>
                            @endif
                            
                            <p class="mt-3 text-muted">
                                <i class='bx bx-shield-quarter'></i> 
                                Secure payment powered by Razorpay
                            </p>
                            <img src="https://razorpay.com/assets/razorpay-logo.svg" alt="Razorpay" style="height: 30px;" class="mt-2">
                        </div>
                        
                        <hr>
                        
                        <div class="text-center">
                            <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                                <i class='bx bx-x'></i> Cancel Payment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Payment Area End -->

<!-- Razorpay Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
// Ensure jQuery is loaded
if (typeof jQuery === 'undefined') {
    console.error('jQuery is not loaded!');
}

jQuery(document).ready(function($) {
    var bookingId = {{ $booking->id }};
    var orderId = null;
    
    // Check if Razorpay is loaded
    if (typeof Razorpay === 'undefined') {
        console.error('Razorpay SDK is not loaded!');
        $('#rzp-button').prop('disabled', true).html('<i class="bx bx-error"></i> Payment SDK Not Loaded');
    }
    
    $('#rzp-button').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Disable button and show loading
        var $button = $(this);
        $button.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Creating Order...');
        
        // Create Razorpay order
        $.ajax({
            url: '{{ route("razorpay.create.order") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                booking_id: bookingId
            },
            dataType: 'json',
            success: function(response) {
                console.log('Order created:', response);
                if (response.success && response.order_id) {
                    orderId = response.order_id;
                    openRazorpay(response);
                } else {
                    alert('Error creating order: ' + (response.message || 'Unknown error'));
                    $button.prop('disabled', false).html('<i class="bx bx-lock-alt"></i> Pay with Razorpay');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr, status, error);
                var errorMsg = 'Something went wrong';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                    } catch(e) {
                        errorMsg = xhr.statusText || error || 'Network error';
                    }
                }
                alert('Error: ' + errorMsg + '\n\nPlease try the "Test Payment" button below for development testing.');
                $button.prop('disabled', false).html('<i class="bx bx-lock-alt"></i> Pay with Razorpay');
            }
        });
        
        return false;
    });
    
    function openRazorpay(orderData) {
        var options = {
            "key": "{{ env('RAZORPAY_KEY') }}",
            "amount": orderData.amount,
            "currency": orderData.currency,
            "name": "{{ config('app.name') }}",
            "description": "Booking #{{ $booking->code }}",
            "image": "{{ asset('upload/logo.png') }}",
            "order_id": orderData.order_id,
            "handler": function (response) {
                console.log('Payment successful:', response);
                // Payment successful, submit to server
                var form = $('<form action="{{ route("razorpay.process") }}" method="POST" style="display:none;">' +
                    '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                    '<input type="hidden" name="razorpay_payment_id" value="' + response.razorpay_payment_id + '">' +
                    '<input type="hidden" name="razorpay_order_id" value="' + response.razorpay_order_id + '">' +
                    '<input type="hidden" name="razorpay_signature" value="' + response.razorpay_signature + '">' +
                    '<input type="hidden" name="booking_id" value="' + bookingId + '">' +
                    '</form>');
                $('body').append(form);
                form[0].submit(); // Use native submit instead of jQuery submit
            },
            "prefill": {
                "name": "{{ $booking->name }}",
                "email": "{{ $booking->email }}",
                "contact": "{{ $booking->phone }}"
            },
            "notes": {
                "booking_id": "{{ $booking->id }}",
                "booking_code": "{{ $booking->code }}"
            },
            "theme": {
                "color": "#3399cc"
            },
            "modal": {
                "ondismiss": function() {
                    $('#rzp-button').prop('disabled', false).html('<i class="bx bx-lock-alt"></i> Pay with Razorpay');
                }
            }
        };
        
        var rzp = new Razorpay(options);
        
        rzp.on('payment.failed', function (response) {
            alert('Payment failed: ' + response.error.description);
            $('#rzp-button').prop('disabled', false).html('<i class="bx bx-lock-alt"></i> Pay with Razorpay');
        });
        
        rzp.open();
    }
});
</script>

@endsection
