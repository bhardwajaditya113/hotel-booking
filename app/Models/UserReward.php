<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReward extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reward()
    {
        return $this->belongsTo(LoyaltyReward::class, 'loyalty_reward_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function transaction()
    {
        return $this->belongsTo(LoyaltyTransaction::class, 'loyalty_transaction_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    // Status checks
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isUsed()
    {
        return $this->status === 'used';
    }

    public function isExpired()
    {
        return $this->status === 'expired' || 
               ($this->expires_at && $this->expires_at->isPast());
    }

    public function isValid()
    {
        return $this->isActive() && !$this->isExpired();
    }

    // Use reward
    public function use($bookingId = null)
    {
        if (!$this->isValid()) {
            return false;
        }

        $this->update([
            'status' => 'used',
            'used_at' => now(),
            'booking_id' => $bookingId ?? $this->booking_id,
        ]);

        return true;
    }

    // Cancel/Expire reward
    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
        return $this;
    }

    public function expire()
    {
        $this->update(['status' => 'expired']);
        return $this;
    }

    // Get status badge
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'used' => 'secondary',
            'expired' => 'danger',
            'cancelled' => 'warning',
            default => 'secondary',
        };
    }

    // Get days until expiry
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expires_at) return null;
        if ($this->expires_at->isPast()) return 0;
        return $this->expires_at->diffInDays(now());
    }

    // Find reward by code
    public static function findByCode($code)
    {
        return self::where('code', strtoupper($code))->first();
    }
}
