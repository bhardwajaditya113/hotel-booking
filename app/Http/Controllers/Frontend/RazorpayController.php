<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Services\Marketplace\BookingMarketplacePaymentCompleter;
use App\Services\Marketplace\BookingMarketplaceSplitCalculator;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class RazorpayController extends Controller
{
    /**
     * Display Razorpay payment page
     */
    public function paymentPage($booking_id)
    {
        $booking = Booking::with(['room', 'property'])->findOrFail($booking_id);

        // Check if booking belongs to current user
        if ($booking->user_id != auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check if already paid
        if ($booking->payment_status == 1) {
            return redirect()->route('user.booking')->with([
                'message' => 'This booking is already paid',
                'alert-type' => 'info',
            ]);
        }

        if ($booking->awaitsHostApproval()) {
            return redirect()->route('booking.confirmation', ['booking_id' => $booking->id])->with([
                'message' => __('frontend.payment.wait_host_approve'),
                'alert-type' => 'warning',
            ]);
        }

        if ($booking->host_approval_status === 'declined') {
            return redirect()->route('booking.confirmation', ['booking_id' => $booking->id])->with([
                'message' => __('frontend.payment.booking_declined'),
                'alert-type' => 'error',
            ]);
        }

        return view('frontend.payment.razorpay', compact('booking'));
    }

    /**
     * Process Razorpay payment
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_signature' => 'required',
            'booking_id' => 'required',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        if ($booking->user_id != auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($booking->awaitsHostApproval()) {
            return redirect()->route('booking.confirmation', ['booking_id' => $booking->id])->with([
                'message' => __('frontend.payment.wait_host_approve'),
                'alert-type' => 'warning',
            ]);
        }

        if ($booking->host_approval_status === 'declined') {
            return redirect()->route('booking.confirmation', ['booking_id' => $booking->id])->with([
                'message' => __('frontend.payment.booking_declined'),
                'alert-type' => 'error',
            ]);
        }

        // Check if this is a test payment (for development)
        $isTestPayment = $request->has('test_payment') && $request->test_payment == 'true';

        if ($isTestPayment) {
            // Skip signature verification for test payments
            // This allows testing without actual Razorpay integration
        } else {
            // Verify signature
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            try {
                $attributes = [
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature,
                ];

                $api->utility->verifyPaymentSignature($attributes);
            } catch (\Exception $e) {
                return redirect('/')->with([
                    'message' => 'Payment verification failed: '.$e->getMessage(),
                    'alert-type' => 'error',
                ]);
            }
        }

        try {
            BookingMarketplacePaymentCompleter::complete(
                $booking,
                $request->razorpay_payment_id,
                $request->razorpay_order_id,
                [
                    'source' => 'razorpay_checkout_redirect',
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                ]
            );

            return redirect()->route('booking.confirmation', ['booking_id' => $booking->id])->with([
                'message' => 'Payment Successful! Your booking is confirmed.',
                'alert-type' => 'success',
            ]);

        } catch (\Exception $e) {
            \Log::error('Payment processing error: '.$e->getMessage());

            return redirect('/')->with([
                'message' => 'Payment processing failed: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Create Razorpay order (optional Route transfers for marketplace payout split).
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::with(['property.host.hostProfile'])->findOrFail($request->booking_id);

        if ($booking->user_id != auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($booking->awaitsHostApproval()) {
            return response()->json([
                'success' => false,
                'message' => __('frontend.payment.wait_host_approve'),
            ], 422);
        }

        if ($booking->host_approval_status === 'declined') {
            return response()->json([
                'success' => false,
                'message' => __('frontend.payment.booking_declined'),
            ], 422);
        }

        $guestChargeInr = BookingMarketplaceSplitCalculator::guestTotalInrFromBooking($booking);
        $split = BookingMarketplaceSplitCalculator::persistBookingSnapshot($booking, $guestChargeInr);

        $linkedAccountId = optional($booking->property?->host?->hostProfile)->razorpay_linked_account_id;

        $orderPayload = [
            'receipt' => 'booking_'.$booking->id,
            'amount' => $split['total_paise'],
            'currency' => 'INR',
            'notes' => [
                'booking_id' => (string) $booking->id,
                'booking_code' => (string) ($booking->code ?? ''),
            ],
        ];

        if (
            config('marketplace.route_enabled')
            && $linkedAccountId
            && ($split['host_transfer_paise'] ?? 0) > 0
        ) {
            $transfer = [
                'account' => $linkedAccountId,
                'amount' => $split['host_transfer_paise'],
                'currency' => 'INR',
            ];
            if (config('marketplace.transfer_on_hold')) {
                $transfer['on_hold'] = 1;
            }
            $orderPayload['transfers'] = [$transfer];
            $booking->marketplace_route_transfer_used = true;
            $booking->save();
        }

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        try {
            $order = $api->order->create($orderPayload);

            $booking->razorpay_order_id = $order['id'];
            $booking->save();

            return response()->json([
                'success' => true,
                'order_id' => $order['id'],
                'amount' => $split['total_paise'],
                'currency' => 'INR',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle payment failure
     */
    public function paymentFailed(Request $request)
    {
        return redirect('/')->with([
            'message' => 'Payment was cancelled or failed. Please try again.',
            'alert-type' => 'error',
        ]);
    }

    /**
     * Test payment bypass (for development only)
     * This allows completing payment without actual Razorpay integration
     */
    public function testPayment(Request $request)
    {
        if (app()->environment('production')) {
            abort(403, 'Test payment not allowed in production');
        }

        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        // Check if booking belongs to current user
        if ($booking->user_id != auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check if already paid
        if ($booking->payment_status == 1) {
            return redirect()->route('booking.confirmation', ['booking_id' => $booking->id])->with([
                'message' => 'This booking is already paid.',
                'alert-type' => 'info',
            ]);
        }

        if ($booking->awaitsHostApproval()) {
            return redirect()->route('booking.confirmation', ['booking_id' => $booking->id])->with([
                'message' => __('frontend.payment.wait_host_approve'),
                'alert-type' => 'warning',
            ]);
        }

        if ($booking->host_approval_status === 'declined') {
            return redirect()->route('booking.confirmation', ['booking_id' => $booking->id])->with([
                'message' => __('frontend.payment.booking_declined'),
                'alert-type' => 'error',
            ]);
        }

        $guestChargeInr = BookingMarketplaceSplitCalculator::guestTotalInrFromBooking($booking);
        BookingMarketplaceSplitCalculator::persistBookingSnapshot($booking, $guestChargeInr);

        $paymentId = 'test_txn_'.time();

        try {
            BookingMarketplacePaymentCompleter::complete($booking, $paymentId, $booking->razorpay_order_id, [
                'source' => 'razorpay_test_payment',
            ]);

            return redirect()->route('booking.confirmation', ['booking_id' => $booking->id])->with([
                'message' => 'Test Payment Successful! Your booking is confirmed.',
                'alert-type' => 'success',
            ]);
        } catch (\Throwable $e) {
            \Log::error('Test payment completion failed: '.$e->getMessage());

            return redirect('/')->with([
                'message' => 'Test payment failed: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }
}
