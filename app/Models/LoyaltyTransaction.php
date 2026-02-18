<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Scopes
    public function scopeEarned($query)
    {
        return $query->where('type', 'earn');
    }

    public function scopeRedeemed($query)
    {
        return $query->where('type', 'redeem');
    }

    public function scopeExpiring($query, $days = 30)
    {
        return $query->where('type', 'earn')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays($days))
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    // Check if expired
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // Check if expiring soon
    public function isExpiringSoon($days = 30)
    {
        return $this->expires_at && 
               $this->expires_at->isFuture() && 
               $this->expires_at->diffInDays(now()) <= $days;
    }

    // Get type badge color
    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            'earn' => 'success',
            'redeem' => 'warning',
            'expire' => 'danger',
            'bonus' => 'info',
            'adjustment' => 'secondary',
            'referral' => 'primary',
            default => 'secondary',
        };
    }

    // Get type icon
    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'earn' => 'fa-plus-circle',
            'redeem' => 'fa-minus-circle',
            'expire' => 'fa-clock',
            'bonus' => 'fa-gift',
            'adjustment' => 'fa-edit',
            'referral' => 'fa-user-plus',
            default => 'fa-circle',
        };
    }
}
