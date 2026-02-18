<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoyalty extends Model
{
    use HasFactory;

    protected $table = 'user_loyalty';

    protected $guarded = [];

    protected $casts = [
        'tier_upgraded_at' => 'datetime',
        'tier_expires_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tier()
    {
        return $this->belongsTo(LoyaltyTier::class, 'loyalty_tier_id');
    }

    public function transactions()
    {
        return $this->hasMany(LoyaltyTransaction::class, 'user_id', 'user_id');
    }

    // Get or create for user
    public static function getOrCreate($userId)
    {
        $loyalty = self::where('user_id', $userId)->first();
        
        if (!$loyalty) {
            $defaultTier = LoyaltyTier::active()->orderBy('min_points')->first();
            
            $loyalty = self::create([
                'user_id' => $userId,
                'loyalty_tier_id' => $defaultTier->id ?? 1,
                'total_points' => 0,
                'available_points' => 0,
                'lifetime_points' => 0,
                'total_bookings' => 0,
                'total_spent' => 0,
            ]);
        }
        
        return $loyalty;
    }

    // Earn points
    public function earnPoints($points, $description, $bookingId = null, $type = 'earn')
    {
        // Apply tier multiplier
        $multiplier = $this->tier ? $this->tier->multiplier_decimal : 1;
        $actualPoints = (int) round($points * $multiplier);

        $this->increment('total_points', $actualPoints);
        $this->increment('available_points', $actualPoints);
        $this->increment('lifetime_points', $actualPoints);

        // Log transaction
        LoyaltyTransaction::create([
            'user_id' => $this->user_id,
            'booking_id' => $bookingId,
            'type' => $type,
            'points' => $actualPoints,
            'balance_after' => $this->available_points,
            'description' => $description,
            'reference_type' => $bookingId ? 'booking' : null,
            'reference_id' => $bookingId,
            'expires_at' => now()->addYear(), // Points expire after 1 year
        ]);

        // Check for tier upgrade
        $this->checkTierUpgrade();

        return $actualPoints;
    }

    // Redeem points
    public function redeemPoints($points, $description, $bookingId = null)
    {
        if ($this->available_points < $points) {
            return false;
        }

        $this->decrement('available_points', $points);

        LoyaltyTransaction::create([
            'user_id' => $this->user_id,
            'booking_id' => $bookingId,
            'type' => 'redeem',
            'points' => -$points,
            'balance_after' => $this->available_points,
            'description' => $description,
            'reference_type' => $bookingId ? 'booking' : null,
            'reference_id' => $bookingId,
        ]);

        return true;
    }

    // Check and upgrade tier
    public function checkTierUpgrade()
    {
        $newTier = LoyaltyTier::getTierByPoints($this->lifetime_points);
        
        if ($newTier && $newTier->id !== $this->loyalty_tier_id) {
            $oldTier = $this->tier;
            
            $this->update([
                'loyalty_tier_id' => $newTier->id,
                'tier_upgraded_at' => now(),
            ]);

            // TODO: Send tier upgrade notification
            
            return $newTier;
        }
        
        return null;
    }

    // Get progress to next tier
    public function getNextTierProgressAttribute()
    {
        $nextTier = $this->tier?->next_tier;
        
        if (!$nextTier) {
            return ['percentage' => 100, 'points_needed' => 0, 'next_tier' => null];
        }

        $pointsNeeded = $nextTier->min_points - $this->lifetime_points;
        $currentTierPoints = $this->tier->min_points;
        $pointsRange = $nextTier->min_points - $currentTierPoints;
        $pointsEarned = $this->lifetime_points - $currentTierPoints;
        $percentage = $pointsRange > 0 ? min(100, ($pointsEarned / $pointsRange) * 100) : 0;

        return [
            'percentage' => round($percentage, 1),
            'points_needed' => max(0, $pointsNeeded),
            'next_tier' => $nextTier,
        ];
    }

    // Record a booking
    public function recordBooking($amount)
    {
        $this->increment('total_bookings');
        $this->increment('total_spent', $amount);
    }

    // Get discount for user's tier
    public function getTierDiscountAttribute()
    {
        return $this->tier ? $this->tier->discount_percentage : 0;
    }

    // Calculate points value (1 point = 0.25 INR by default)
    public function getPointsValueAttribute()
    {
        return $this->available_points * 0.25;
    }
}
