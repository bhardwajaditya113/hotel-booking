<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PaymentTransaction;
use App\Services\Marketplace\BookingMarketplacePaymentCompleter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class RazorpayWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $secret = config('services.razorpay.webhook_secret');
        if ($secret === null || $secret === '') {
            Log::warning('Razorpay webhook rejected: RAZORPAY_WEBHOOK_SECRET not configured');

            return response('Webhook not configured', 503);
        }

        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');
        $expected = hash_hmac('sha256', $payload, $secret);

        if ($signature === null || ! hash_equals($expected, $signature)) {
            return response('Invalid signature', 400);
        }

        /** @var array<string, mixed>|null $data */
        $data = json_decode($payload, true);
        if (! is_array($data)) {
            return response('Invalid payload', 400);
        }

        $event = (string) ($data['event'] ?? '');

        try {
            match (true) {
                $event === 'payment.captured' => $this->handlePaymentCaptured($data),
                str_starts_with($event, 'refund.') => $this->handleRefundEvent($data),
                str_starts_with($event, 'payment.dispute.') => $this->handlePaymentDisputeEvent($data),
                default => null,
            };
        } catch (\Throwable $e) {
            Log::error('Razorpay webhook handler error: '.$e->getMessage(), ['event' => $event]);

            return response('Handler error', 500);
        }

        return response('OK', 200);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function handlePaymentCaptured(array $data): void
    {
        $payment = data_get($data, 'payload.payment.entity');
        if (! is_array($payment)) {
            return;
        }

        $paymentId = isset($payment['id']) ? (string) $payment['id'] : null;
        $orderId = isset($payment['order_id']) ? (string) $payment['order_id'] : null;
        if ($paymentId === null || $paymentId === '') {
            return;
        }

        $notes = data_get($payment, 'notes');
        $bookingId = null;
        if (is_array($notes) && isset($notes['booking_id'])) {
            $bookingId = is_numeric($notes['booking_id']) ? (int) $notes['booking_id'] : null;
        }

        $booking = null;
        if ($bookingId) {
            $booking = Booking::query()->find($bookingId);
        }
        if (! $booking && $orderId) {
            $booking = Booking::query()->where('razorpay_order_id', $orderId)->first();
        }

        if (! $booking) {
            Log::info('Razorpay payment.captured: no booking matched', ['payment_id' => $paymentId, 'order_id' => $orderId]);

            return;
        }

        BookingMarketplacePaymentCompleter::complete($booking, $paymentId, $orderId, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function handleRefundEvent(array $data): void
    {
        $refund = data_get($data, 'payload.refund.entity');
        if (! is_array($refund)) {
            return;
        }

        $paymentId = isset($refund['payment_id']) ? (string) $refund['payment_id'] : null;
        if ($paymentId === null || $paymentId === '') {
            return;
        }

        $booking = Booking::query()->where('transation_id', $paymentId)->first()
            ?? Booking::query()->whereHas('paymentTransactions', static function ($q) use ($paymentId) {
                $q->where('provider_transaction_id', $paymentId);
            })->first();

        if (! $booking) {
            Log::info('Razorpay refund webhook: booking not found', ['payment_id' => $paymentId]);

            return;
        }

        DB::transaction(function () use ($booking, $paymentId, $refund, $data) {
            $txn = PaymentTransaction::query()
                ->where('provider_transaction_id', $paymentId)
                ->first();

            $newSettlement = 'refunded';
            $markTxnRefunded = true;

            if ($txn !== null && isset($refund['amount'])) {
                $refundPaise = (int) $refund['amount'];
                $paidPaise = (int) round((float) $txn->amount * 100);
                if ($paidPaise > 0 && $refundPaise > 0 && $refundPaise < $paidPaise) {
                    $newSettlement = 'partial_refunded';
                    $markTxnRefunded = false;
                }
            }

            $booking->refresh();
            $booking->marketplace_settlement_status = $newSettlement;
            $booking->save();

            if ($txn !== null) {
                $prevRaw = $txn->provider_response;
                $prevArr = [];
                if (is_array($prevRaw)) {
                    $prevArr = $prevRaw;
                } elseif (is_string($prevRaw) && $prevRaw !== '') {
                    $decoded = json_decode($prevRaw, true);
                    $prevArr = is_array($decoded) ? $decoded : [];
                }
                $updates = [
                    'provider_response' => array_merge(
                        $prevArr,
                        ['last_refund_webhook' => $data]
                    ),
                ];
                if ($markTxnRefunded) {
                    $updates['status'] = 'refunded';
                }
                $txn->update($updates);
            }
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function handlePaymentDisputeEvent(array $data): void
    {
        $dispute = data_get($data, 'payload.dispute.entity');
        $paymentEntity = data_get($data, 'payload.payment.entity');

        $paymentId = null;
        if (is_array($dispute) && isset($dispute['payment_id'])) {
            $paymentId = (string) $dispute['payment_id'];
        }
        if (($paymentId === null || $paymentId === '') && is_array($paymentEntity) && isset($paymentEntity['id'])) {
            $paymentId = (string) $paymentEntity['id'];
        }

        if ($paymentId === null || $paymentId === '') {
            return;
        }

        $disputeId = is_array($dispute) && isset($dispute['id']) ? (string) $dispute['id'] : null;

        $booking = Booking::query()->where('transation_id', $paymentId)->first()
            ?? Booking::query()->whereHas('paymentTransactions', static function ($q) use ($paymentId) {
                $q->where('provider_transaction_id', $paymentId);
            })->first();

        if (! $booking) {
            Log::info('Razorpay dispute webhook: booking not found', ['payment_id' => $paymentId]);

            return;
        }

        $booking->marketplace_dispute_id = $disputeId ?? $booking->marketplace_dispute_id;
        $booking->marketplace_settlement_status = 'disputed';
        $booking->save();
    }
}
