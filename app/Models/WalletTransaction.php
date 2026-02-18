<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Scopes
    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeDebits($query)
    {
        return $query->where('type', 'debit');
    }

    public function scopeOfType($query, $transactionType)
    {
        return $query->where('transaction_type', $transactionType);
    }

    // Type helpers
    public function isCredit()
    {
        return $this->type === 'credit';
    }

    public function isDebit()
    {
        return $this->type === 'debit';
    }

    // Get formatted amount with sign
    public function getFormattedAmountAttribute()
    {
        $sign = $this->isCredit() ? '+' : '-';
        return $sign . '₹' . number_format($this->amount, 2);
    }

    // Get type badge
    public function getTypeBadgeAttribute()
    {
        return $this->isCredit() ? 'success' : 'danger';
    }

    // Get transaction type icon
    public function getTypeIconAttribute()
    {
        return match($this->transaction_type) {
            'deposit' => 'fa-wallet',
            'refund' => 'fa-undo',
            'payment' => 'fa-shopping-cart',
            'cashback' => 'fa-percentage',
            'reward' => 'fa-gift',
            'referral' => 'fa-user-plus',
            'adjustment' => 'fa-edit',
            'withdrawal' => 'fa-money-bill-transfer',
            'expired' => 'fa-clock',
            default => 'fa-exchange-alt',
        };
    }

    // Get transaction type display name
    public function getTransactionTypeDisplayAttribute()
    {
        return match($this->transaction_type) {
            'deposit' => 'Wallet Top-up',
            'refund' => 'Booking Refund',
            'payment' => 'Booking Payment',
            'cashback' => 'Cashback',
            'reward' => 'Reward Credit',
            'referral' => 'Referral Bonus',
            'adjustment' => 'Adjustment',
            'withdrawal' => 'Withdrawal',
            'expired' => 'Expired Credit',
            default => ucfirst($this->transaction_type),
        };
    }
}
