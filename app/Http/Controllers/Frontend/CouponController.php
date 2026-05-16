<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    /**
     * Apply coupon code
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code',
            ]);
        }

        // Validate the coupon
        $validation = $coupon->canBeUsedBy(Auth::id(), $request->room_id, (float) $request->amount, null);

        if (! $validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validation['message'],
            ]);
        }

        // Calculate discount
        $discount = $coupon->calculateDiscount($request->amount);

        return response()->json([
            'success' => true,
            'coupon_id' => $coupon->id,
            'code' => $coupon->code,
            'discount' => $discount,
            'type' => $coupon->type,
            'message' => 'Coupon applied! You save ₹'.number_format($discount, 2),
        ]);
    }

    /**
     * Remove applied coupon
     */
    public function remove(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Coupon removed',
        ]);
    }

    /**
     * Record coupon usage after successful booking
     */
    public static function recordUsage($couponId, $userId, $bookingId, $discountAmount)
    {
        if (! $couponId) {
            return;
        }

        CouponUsage::create([
            'coupon_id' => $couponId,
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'discount_amount' => $discountAmount,
        ]);

        // Increment usage count
        Coupon::where('id', $couponId)->increment('times_used');
    }

    /**
     * List valid coupons for checkout / wallet UI (schema-aligned with coupons table).
     */
    public function available(Request $request)
    {
        $coupons = Coupon::query()
            ->active()
            ->where(function ($query) {
                $query->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            })
            ->where(function ($query) {
                $query->whereNull('total_uses')
                    ->orWhereRaw('times_used < total_uses');
            })
            ->orderByDesc('value')
            ->get()
            ->map(function ($coupon) {
                return [
                    'code' => $coupon->code,
                    'description' => $coupon->description,
                    'discount' => $coupon->type === 'percentage'
                        ? $coupon->value.'% off'
                        : '₹'.number_format((float) $coupon->value, 2).' off',
                    'min_amount' => $coupon->min_booking_amount,
                    'max_discount' => $coupon->max_discount,
                    'valid_until' => $coupon->valid_until ? $coupon->valid_until->format('M d, Y') : null,
                ];
            });

        return response()->json([
            'coupons' => $coupons,
        ]);
    }
}
