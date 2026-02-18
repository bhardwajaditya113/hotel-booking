<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'card_details' => 'encrypted:array',
        'is_default' => 'boolean',
        'is_verified' => 'boolean',
    ];

    protected $hidden = ['card_details'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // Get default for user
    public static function getDefault($userId)
    {
        return self::where('user_id', $userId)
            ->verified()
            ->where('is_default', true)
            ->first();
    }

    // Set as default
    public function setAsDefault()
    {
        // Remove default from other methods
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        
        $this->update(['is_default' => true]);
        return $this;
    }

    // Get card display name (masked)
    public function getMaskedCardAttribute()
    {
        if ($this->type !== 'card') return null;
        
        $details = $this->card_details ?? [];
        $last4 = $details['last4'] ?? '****';
        $brand = $details['brand'] ?? 'Card';
        
        return ucfirst($brand) . ' •••• ' . $last4;
    }

    // Get display name
    public function getDisplayNameAttribute()
    {
        if ($this->nickname) return $this->nickname;
        
        return match($this->type) {
            'card' => $this->masked_card,
            'upi' => $this->provider_reference,
            'bank' => 'Bank Account •••• ' . substr($this->provider_reference ?? '', -4),
            default => $this->paymentMethod->name ?? 'Payment Method',
        };
    }

    // Get card brand icon
    public function getCardIconAttribute()
    {
        if ($this->type !== 'card') return 'fa-credit-card';
        
        $brand = $this->card_details['brand'] ?? '';
        
        return match(strtolower($brand)) {
            'visa' => 'fa-cc-visa',
            'mastercard' => 'fa-cc-mastercard',
            'amex', 'american express' => 'fa-cc-amex',
            'discover' => 'fa-cc-discover',
            'diners' => 'fa-cc-diners-club',
            'jcb' => 'fa-cc-jcb',
            default => 'fa-credit-card',
        };
    }
}
