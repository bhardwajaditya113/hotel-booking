<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingCancellation;
use App\Models\CancellationPolicy;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CancellationController extends Controller
{
    /**
     * Show cancellation form
     */
    public function show($bookingId)
    {
        $booking = Booking::with(['room.cancellationPolicy', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($bookingId);
        
        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'This booking cannot be cancelled');
        }
        
        $policy = $booking->room?->cancellationPolicy ?? CancellationPolicy::getDefault();
        $refundBreakdown = $this->calculateRefundBreakdown($booking, $policy);
        
        return view('frontend.cancellation.show', compact('booking', 'policy', 'refundBreakdown'));
    }

    /**
     * Calculate refund breakdown
     */
    protected function calculateRefundBreakdown($booking, $policy)
    {
        $checkIn = $booking->check_in;
        $hoursUntilCheckIn = now()->diffInHours($checkIn, false);
        $daysUntilCheckIn = now()->diffInDays($checkIn, false);
        
        $refundPercentage = 0;
        $applicableRule = null;
        
        if ($policy) {
            if ($hoursUntilCheckIn <= $policy->hours_before_full_charge) {
                $refundPercentage = 0;
                $applicableRule = 'Full charge applies (within ' . $policy->hours_before_full_charge . ' hours)';
            } elseif ($daysUntilCheckIn <= $policy->days_before_partial_refund) {
                $refundPercentage = $policy->partial_refund_percentage;
                $applicableRule = 'Partial refund (' . $policy->partial_refund_percentage . '%)';
            } else {
                $refundPercentage = $policy->full_refund_percentage;
                $applicableRule = 'Full refund (' . $policy->full_refund_percentage . '%)';
            }
        }
        
        $originalAmount = $booking->total_price;
        $refundAmount = ($originalAmount * $refundPercentage) / 100;
        $penaltyAmount = $originalAmount - $refundAmount;
        
        return [
            'original_amount' => $originalAmount,
            'refund_percentage' => $refundPercentage,
            'refund_amount' => round($refundAmount, 2),
            'penalty_amount' => round($penaltyAmount, 2),
            'applicable_rule' => $applicableRule,
            'hours_until_checkin' => max(0, $hoursUntilCheckIn),
            'days_until_checkin' => max(0, $daysUntilCheckIn),
            'policy_name' => $policy?->name ?? 'Standard Policy',
        ];
    }

    /**
     * Process cancellation
     */
    public function process(Request $request, $bookingId)
    {
        $booking = Booking::with(['room.cancellationPolicy', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($bookingId);
        
        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'This booking cannot be cancelled');
        }
        
        $validated = $request->validate([
            'reason' => 'nullable|string|max:1000',
            'reason_type' => 'nullable|in:change_of_plans,found_better_option,emergency,other',
        ]);
        
        $reason = $validated['reason_type'] ?? 'other';
        if ($validated['reason']) {
            $reason .= ': ' . $validated['reason'];
        }
        
        try {
            // Process the cancellation
            $cancellation = $booking->processCancellation(Auth::id(), $reason);
            
            // Process refund
            $this->processRefund($booking, $cancellation);
            
            // Send notification
            Notification::send(
                $booking->user_id,
                'booking-cancelled',
                [
                    'booking_code' => $booking->code,
                    'guest_name' => $booking->name,
                    'hotel_name' => config('app.name'),
                    'refund_amount' => '₹' . number_format($cancellation->refund_amount, 2),
                ],
                $booking->id
            );
            
            return redirect()->route('user.bookings')
                ->with('success', 'Booking cancelled successfully. Refund of ₹' . number_format($cancellation->refund_amount, 2) . ' will be processed.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel booking: ' . $e->getMessage());
        }
    }

    /**
     * Process refund to original payment method or wallet
     */
    protected function processRefund($booking, $cancellation)
    {
        if ($cancellation->refund_amount <= 0) {
            $cancellation->update(['status' => 'no_refund']);
            return;
        }
        
        // Find the original payment
        $originalPayment = $booking->successfulPayments()->first();
        
        $refundMethod = request('refund_method', 'wallet'); // wallet or original
        
        if ($refundMethod === 'wallet' || !$originalPayment) {
            // Credit to wallet
            $wallet = $booking->user->getOrCreateWallet();
            $wallet->credit(
                $cancellation->refund_amount,
                'refund',
                "Refund for cancelled booking #{$booking->code}",
                $booking->id
            );
            
            $cancellation->update([
                'status' => 'refunded',
                'refund_method' => 'wallet',
                'processed_at' => now(),
            ]);
        } else {
            // Try to refund to original payment method
            try {
                $this->refundToOriginalPayment($originalPayment, $cancellation->refund_amount);
                
                $cancellation->update([
                    'status' => 'refunded',
                    'refund_method' => 'original',
                    'processed_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Fallback to wallet
                $wallet = $booking->user->getOrCreateWallet();
                $wallet->credit(
                    $cancellation->refund_amount,
                    'refund',
                    "Refund for cancelled booking #{$booking->code} (original refund failed)",
                    $booking->id
                );
                
                $cancellation->update([
                    'status' => 'refunded',
                    'refund_method' => 'wallet',
                    'processed_at' => now(),
                    'refund_notes' => 'Original payment refund failed: ' . $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Refund to original payment method
     */
    protected function refundToOriginalPayment($payment, $amount)
    {
        $provider = $payment->paymentMethod?->provider;
        
        switch ($provider) {
            case 'stripe':
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                \Stripe\Refund::create([
                    'payment_intent' => $payment->provider_reference,
                    'amount' => $amount * 100,
                ]);
                break;
                
            case 'razorpay':
                $api = new \Razorpay\Api\Api(
                    config('services.razorpay.key'),
                    config('services.razorpay.secret')
                );
                $api->payment->fetch($payment->provider_reference)->refund([
                    'amount' => $amount * 100,
                ]);
                break;
                
            default:
                throw new \Exception('Refund not supported for this payment method');
        }
        
        $payment->processRefund($amount, Auth::id(), 'Booking cancellation refund');
    }

    /**
     * View cancellation details
     */
    public function details($cancellationId)
    {
        $cancellation = BookingCancellation::with(['booking.room', 'policy'])
            ->whereHas('booking', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->findOrFail($cancellationId);
        
        return view('frontend.cancellation.details', compact('cancellation'));
    }

    /**
     * Get cancellation policy for a room (AJAX)
     */
    public function getPolicy($roomId)
    {
        $policy = CancellationPolicy::whereHas('rooms', function ($q) use ($roomId) {
            $q->where('id', $roomId);
        })->first() ?? CancellationPolicy::getDefault();
        
        return response()->json([
            'policy' => $policy,
            'summary' => $policy ? $policy->getSummary() : null,
        ]);
    }

    /**
     * View all cancellation policies
     */
    public function policies()
    {
        $policies = CancellationPolicy::active()
            ->orderBy('name')
            ->get();
        
        return view('frontend.cancellation.policies', compact('policies'));
    }
}
