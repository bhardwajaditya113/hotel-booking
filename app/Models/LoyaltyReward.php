<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyReward extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'conditions' => 'array',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // Relationships
    public function minTier()
    {
        return $this->belongsTo(LoyaltyTier::class, 'min_tier_id');
    }

    public function redemptions()
    {
        return $this->hasMany(UserReward::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
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
                $q->whereNull('quantity_available')
                  ->orWhereRaw('quantity_redeemed < quantity_available');
            });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeForTier($query, $tierId)
    {
        return $query->where(function ($q) use ($tierId) {
            $q->whereNull('min_tier_id')
              ->orWhere('min_tier_id', '<=', $tierId);
        });
    }

    // Check availability
    public function isAvailable()
    {
        if (!$this->is_active) return false;
        
        if ($this->valid_from && $this->valid_from->isFuture()) return false;
        if ($this->valid_until && $this->valid_until->isPast()) return false;
        
        if ($this->quantity_available !== null && 
            $this->quantity_redeemed >= $this->quantity_available) {
            return false;
        }
        
        return true;
    }

    // Get remaining quantity
    public function getRemainingQuantityAttribute()
    {
        if ($this->quantity_available === null) return null;
        return max(0, $this->quantity_available - $this->quantity_redeemed);
    }

    // Check if user can redeem
    public function canBeRedeemedBy($userLoyalty)
    {
        if (!$this->isAvailable()) return false;
        
        // Check points
        if ($userLoyalty->available_points < $this->points_required) return false;
        
        // Check tier
        if ($this->min_tier_id && $userLoyalty->loyalty_tier_id < $this->min_tier_id) {
            return false;
        }
        
        return true;
    }

    // Redeem reward
    public function redeem($userId, $bookingId = null)
    {
        $userLoyalty = UserLoyalty::getOrCreate($userId);
        
        if (!$this->canBeRedeemedBy($userLoyalty)) {
            return null;
        }

        // Deduct points
        $transaction = $userLoyalty->redeemPoints(
            $this->points_required,
            "Redeemed: {$this->name}",
            $bookingId
        );

        if (!$transaction) return null;

        // Increment redemption count
        $this->increment('quantity_redeemed');

        // Create user reward
        $userReward = UserReward::create([
            'user_id' => $userId,
            'loyalty_reward_id' => $this->id,
            'booking_id' => $bookingId,
            'loyalty_transaction_id' => $transaction->id ?? null,
            'code' => strtoupper(\Str::random(8)),
            'status' => 'active',
            'expires_at' => now()->addDays(90),
        ]);

        return $userReward;
    }

    // Get type badge
    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            'discount' => 'primary',
            'free_night' => 'success',
            'upgrade' => 'info',
            'amenity' => 'warning',
            'experience' => 'purple',
            'voucher' => 'secondary',
            default => 'secondary',
        };
    }
}
