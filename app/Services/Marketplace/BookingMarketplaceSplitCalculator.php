<?php

namespace App\Services\Marketplace;

use App\Models\Booking;

final class BookingMarketplaceSplitCalculator
{
    /**
     * Match Razorpay checkout amount logic: amounts under 100 are treated as USD-ish and scaled to INR.
     */
    public static function guestTotalInrFromBooking(Booking $booking): float
    {
        $amount = (float) $booking->total_price;
        if ($amount < 100) {
            $amount *= 83;
        }

        return round(max(0, $amount), 2);
    }

    /**
     * Guest pays guestTotalInr. Platform keeps commission + GST-on-commission; remainder is host payout.
     *
     * @return array{
     *     guest_total_inr: float,
     *     platform_fee_base_inr: float,
     *     platform_gst_inr: float,
     *     platform_total_inr: float,
     *     host_payout_inr: float,
     *     total_paise: int,
     *     host_transfer_paise: int,
     * }
     */
    public static function compute(float $guestTotalInr): array
    {
        $guestTotalInr = max(0, round($guestTotalInr, 2));

        $pct = max(0, (float) config('marketplace.platform_fee_percent', 0));
        $fixed = max(0, (float) config('marketplace.platform_fee_fixed_inr', 0));
        $gstPct = max(0, (float) config('marketplace.gst_percent_on_platform_fee', 0));

        $feeBase = round($guestTotalInr * ($pct / 100) + $fixed, 2);
        $gstOnFee = round($feeBase * ($gstPct / 100), 2);
        $platformTotal = round($feeBase + $gstOnFee, 2);

        $hostPayout = round($guestTotalInr - $platformTotal, 2);
        if ($hostPayout < 0) {
            $hostPayout = 0;
            $platformTotal = $guestTotalInr;
            $feeBase = round($platformTotal / (1 + ($gstPct / 100)), 2);
            $gstOnFee = round($platformTotal - $feeBase, 2);
        }

        $totalPaise = self::inrToPaise($guestTotalInr);
        $hostTransferPaise = self::inrToPaise($hostPayout);

        // Razorpay expects integer paise; reconcile rounding vs guest charge
        $platformRetentionPaise = $totalPaise - $hostTransferPaise;
        if ($platformRetentionPaise < 0) {
            $hostTransferPaise = $totalPaise;
        }

        return [
            'guest_total_inr' => $guestTotalInr,
            'platform_fee_base_inr' => $feeBase,
            'platform_gst_inr' => $gstOnFee,
            'platform_total_inr' => round($guestTotalInr - $hostPayout, 2),
            'host_payout_inr' => $hostPayout,
            'total_paise' => $totalPaise,
            'host_transfer_paise' => $hostTransferPaise,
            'platform_retention_paise' => max(0, $totalPaise - $hostTransferPaise),
        ];
    }

    /**
     * Persist commission / GST / payout snapshot used for Route transfers & accounting.
     *
     * @param  array<string, mixed>|null  $split  Output of compute(); omit to compute from guestTotalInr.
     */
    public static function persistBookingSnapshot(Booking $booking, float $guestChargeInr, ?array $split = null): array
    {
        $split ??= self::compute($guestChargeInr);

        $booking->fill([
            'marketplace_platform_fee_base_inr' => $split['platform_fee_base_inr'],
            'marketplace_platform_gst_inr' => $split['platform_gst_inr'],
            'marketplace_platform_total_inr' => $split['platform_total_inr'],
            'marketplace_host_payout_inr' => $split['host_payout_inr'],
            'marketplace_settlement_status' => 'pending_payment',
            'marketplace_route_transfer_used' => false,
            'marketplace_transfer_ids' => null,
            'marketplace_dispute_id' => null,
        ]);
        $booking->save();

        return $split;
    }

    private static function inrToPaise(float $inr): int
    {
        return (int) round(max(0, $inr) * 100);
    }
}
