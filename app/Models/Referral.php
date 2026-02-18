<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'registered_at' => 'datetime',
        'first_booking_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('referral_code', $code);
    }

    // Generate unique referral code
    public static function generateCode($userId)
    {
        $code = strtoupper(substr(md5($userId . time()), 0, 8));
        
        while (self::where('referral_code', $code)->exists()) {
            $code = strtoupper(substr(md5($userId . time() . rand()), 0, 8));
        }
        
        return $code;
    }

    // Create referral for user
    public static function createForUser($referrerId, $email = null)
    {
        return self::create([
            'referrer_id' => $referrerId,
            'referral_code' => self::generateCode($referrerId),
            'referred_email' => $email,
            'status' => 'pending',
            'expires_at' => now()->addDays(30),
        ]);
    }

    // Process registration
    public function markRegistered($userId)
    {
        $this->update([
            'referred_id' => $userId,
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        return $this;
    }

    // Process first booking
    public function markBooked($bonusPoints = 500, $bonusCredit = 100)
    {
        $this->update([
            'status' => 'booked',
            'first_booking_at' => now(),
            'referrer_points' => $bonusPoints,
            'referred_points' => $bonusPoints,
            'referrer_credit' => $bonusCredit,
            'referred_credit' => $bonusCredit,
        ]);

        // Award points to both users
        if ($this->referrer_id) {
            $referrerLoyalty = UserLoyalty::getOrCreate($this->referrer_id);
            $referrerLoyalty->earnPoints(
                $bonusPoints,
                "Referral bonus: friend made their first booking",
                null,
                'referral'
            );
        }

        if ($this->referred_id) {
            $referredLoyalty = UserLoyalty::getOrCreate($this->referred_id);
            $referredLoyalty->earnPoints(
                $bonusPoints,
                "Welcome bonus: your first booking",
                null,
                'referral'
            );
        }

        return $this;
    }

    // Complete referral (after checkout)
    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return $this;
    }

    // Check if referral is valid
    public function isValid()
    {
        if ($this->status !== 'pending') return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        return true;
    }

    // Get referral by code
    public static function findValidByCode($code)
    {
        return self::where('referral_code', strtoupper($code))
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    // Status badge
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'registered' => 'info',
            'booked' => 'primary',
            'completed' => 'success',
            'expired' => 'danger',
            default => 'secondary',
        };
    }
}
