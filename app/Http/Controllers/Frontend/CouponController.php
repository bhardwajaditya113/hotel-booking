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

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code'
            ]);
        }

        // Validate the coupon
        $validation = $coupon->isValid($request->amount, Auth::id(), $request->room_id);
        
        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validation['message']
            ]);
        }

        // Calculate discount
        $discount = $coupon->calculateDiscount($request->amount);

        return response()->json([
            'success' => true,
            'coupon_id' => $coupon->id,
            'code' => $coupon->code,
            'discount' => $discount,
            'type' => $coupon->discount_type,
            'message' => "Coupon applied! You save ₹" . number_format($discount, 2)
        ]);
    }

    /**
     * Remove applied coupon
     */
    public function remove(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Coupon removed'
        ]);
    }

    /**
     * Record coupon usage after successful booking
     */
    public static function recordUsage($couponId, $userId, $bookingId, $discountAmount)
    {
        if (!$couponId) return;

        CouponUsage::create([
            'coupon_id' => $couponId,
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'discount_amount' => $discountAmount,
        ]);

        // Increment usage count
        Coupon::where('id', $couponId)->increment('used_count');
    }

    /**
     * List available coupons for user
     */
    public function available(Request $request)
    {
        $coupons = Coupon::where('is_active', true)
            ->where(function($query) {
                $query->whereNull('valid_from')
                      ->orWhere('valid_from', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('valid_until')
                      ->orWhere('valid_until', '>=', now());
            })
            ->where(function($query) {
                $query->whereNull('max_uses')
                      ->orWhereRaw('used_count < max_uses');
            })
            ->where('is_public', true)
            ->orderBy('discount_value', 'desc')
            ->get()
            ->map(function($coupon) {
                return [
                    'code' => $coupon->code,
                    'description' => $coupon->description,
                    'discount' => $coupon->discount_type === 'percentage' 
                        ? $coupon->discount_value . '% off' 
                        : '₹' . $coupon->discount_value . ' off',
                    'min_amount' => $coupon->min_booking_amount,
                    'max_discount' => $coupon->max_discount_amount,
                    'valid_until' => $coupon->valid_until ? $coupon->valid_until->format('M d, Y') : null,
                ];
            });

        return response()->json([
            'coupons' => $coupons
        ]);
    }
}
