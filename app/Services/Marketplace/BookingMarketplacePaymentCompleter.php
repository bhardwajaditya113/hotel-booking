<?php

namespace App\Services\Marketplace;

use App\Mail\BookConfirm;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

final class BookingMarketplacePaymentCompleter
{
    /**
     * Idempotent finalize after Razorpay payment succeeds (checkout redirect or webhook).
     */
    public static function complete(Booking $booking, string $razorpayPaymentId, ?string $razorpayOrderId = null, ?array $providerPayload = null): void
    {
        if ((int) $booking->payment_status === 1) {
            return;
        }

        DB::transaction(function () use ($booking, $razorpayPaymentId, $razorpayOrderId, $providerPayload) {
            $booking->refresh();

            if ((int) $booking->payment_status === 1) {
                return;
            }

            $booking->payment_status = 1;
            $booking->status = 1;
            $booking->transation_id = $razorpayPaymentId;
            if ($razorpayOrderId && ! $booking->razorpay_order_id) {
                $booking->razorpay_order_id = $razorpayOrderId;
            }

            if ($booking->marketplace_route_transfer_used) {
                $booking->marketplace_settlement_status = 'routed';
            } elseif ($booking->marketplace_platform_total_inr !== null) {
                $booking->marketplace_settlement_status = 'platform_retained';
            } else {
                $booking->marketplace_settlement_status = 'captured';
            }

            $booking->save();

            PaymentTransaction::query()->firstOrCreate(
                ['provider_transaction_id' => $razorpayPaymentId],
                [
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'transaction_id' => PaymentTransaction::generateTransactionId(),
                    'provider_order_id' => $booking->razorpay_order_id ?? $razorpayOrderId,
                    'type' => 'payment',
                    'status' => 'completed',
                    'amount' => $booking->total_price,
                    'fee' => $booking->marketplace_platform_total_inr ?? 0,
                    'net_amount' => $booking->marketplace_host_payout_inr ?? $booking->total_price,
                    'currency' => 'INR',
                    'gateway_amount' => $booking->total_price,
                    'provider_response' => $providerPayload,
                    'completed_at' => now(),
                ]
            );

            try {
                $mailData = [
                    'check_in' => $booking->check_in,
                    'check_out' => $booking->check_out,
                    'name' => $booking->name,
                    'email' => $booking->email,
                    'phone' => $booking->phone,
                    'booking_code' => $booking->code,
                    'total_price' => $booking->total_price,
                    'room' => $booking->room,
                    'property' => $booking->property,
                ];
                Mail::to($booking->email)->send(new BookConfirm($mailData));
            } catch (\Throwable $e) {
                \Log::error('Failed to send booking confirmation email: '.$e->getMessage());
            }

            foreach (User::where('role', 'admin')->get() as $admin) {
                Notification::query()->create([
                    'user_id' => $admin->id,
                    'type' => 'booking',
                    'title' => 'Booking paid',
                    'message' => 'Booking '.$booking->code.' paid via Razorpay.',
                    'data' => ['booking_id' => $booking->id],
                ]);
            }
        });
    }
}
