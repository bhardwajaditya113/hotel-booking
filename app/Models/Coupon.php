<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'applicable_rooms' => 'array',
        'applicable_room_types' => 'array',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'first_booking_only' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function minLoyaltyTier()
    {
        return $this->belongsTo(LoyaltyTier::class, 'min_loyalty_tier_id');
    }

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('total_uses')
                  ->orWhereRaw('times_used < total_uses');
            });
    }

    // Find coupon by code
    public static function findByCode($code)
    {
        return self::where('code', strtoupper(trim($code)))->first();
    }

    // Validation
    public function isValid()
    {
        if (!$this->is_active) return false;
        
        // Check dates
        if ($this->valid_from && now()->lt($this->valid_from)) return false;
        if ($this->valid_until && now()->gt($this->valid_until)) return false;
        
        // Check total usage
        if ($this->total_uses !== null && $this->times_used >= $this->total_uses) {
            return false;
        }
        
        return true;
    }

    // Check if user can use this coupon
    public function canBeUsedBy($userId, $roomId = null, $bookingAmount = null, $nights = null)
    {
        if (!$this->isValid()) return ['valid' => false, 'message' => 'This coupon is no longer valid.'];

        // Check per-user usage limit
        $userUsageCount = $this->usages()->where('user_id', $userId)->count();
        if ($userUsageCount >= $this->uses_per_user) {
            return ['valid' => false, 'message' => 'You have already used this coupon the maximum number of times.'];
        }

        // Check first booking only
        if ($this->first_booking_only) {
            $userBookings = Booking::where('user_id', $userId)->count();
            if ($userBookings > 0) {
                return ['valid' => false, 'message' => 'This coupon is only valid for first-time bookings.'];
            }
        }

        // Check minimum booking amount
        if ($this->min_booking_amount && $bookingAmount < $this->min_booking_amount) {
            return ['valid' => false, 'message' => "Minimum booking amount of ₹{$this->min_booking_amount} required."];
        }

        // Check minimum nights
        if ($this->min_nights && $nights < $this->min_nights) {
            return ['valid' => false, 'message' => "Minimum {$this->min_nights} nights stay required."];
        }

        // Check applicable rooms
        if ($roomId && $this->applicable_rooms && !in_array($roomId, $this->applicable_rooms)) {
            return ['valid' => false, 'message' => 'This coupon is not valid for the selected room.'];
        }

        // Check loyalty tier
        if ($this->min_loyalty_tier_id) {
            $userLoyalty = UserLoyalty::where('user_id', $userId)->first();
            if (!$userLoyalty || $userLoyalty->loyalty_tier_id < $this->min_loyalty_tier_id) {
                $minTier = LoyaltyTier::find($this->min_loyalty_tier_id);
                return ['valid' => false, 'message' => "This coupon requires {$minTier->name} tier or higher."];
            }
        }

        return ['valid' => true, 'message' => 'Coupon applied successfully!'];
    }

    // Calculate discount
    public function calculateDiscount($amount)
    {
        switch ($this->type) {
            case 'percentage':
                $discount = $amount * ($this->value / 100);
                // Apply max discount cap if set
                if ($this->max_discount && $discount > $this->max_discount) {
                    $discount = $this->max_discount;
                }
                return round($discount, 2);
                
            case 'fixed':
                return min($this->value, $amount);
                
            case 'free_night':
                // Value represents per-night rate to discount
                return $this->value;
                
            case 'free_breakfast':
                return $this->value;
                
            default:
                return 0;
        }
    }

    // Apply coupon (record usage)
    public function apply($userId, $bookingId, $discountAmount)
    {
        CouponUsage::create([
            'coupon_id' => $this->id,
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'discount_amount' => $discountAmount,
        ]);

        $this->increment('times_used');

        return $this;
    }

    // Get remaining uses
    public function getRemainingUsesAttribute()
    {
        if ($this->total_uses === null) return null;
        return max(0, $this->total_uses - $this->times_used);
    }

    // Get days until expiry
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->valid_until) return null;
        if ($this->valid_until->isPast()) return 0;
        return $this->valid_until->diffInDays(now());
    }

    // Type display
    public function getTypeDisplayAttribute()
    {
        return match($this->type) {
            'percentage' => "{$this->value}% off",
            'fixed' => "₹{$this->value} off",
            'free_night' => "Free night",
            'free_breakfast' => "Free breakfast",
            default => $this->type,
        };
    }
}
