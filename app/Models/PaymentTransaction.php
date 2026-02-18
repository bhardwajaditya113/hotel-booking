<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'metadata' => 'array',
        'refunded_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($transaction) {
            $transaction->transaction_id = $transaction->transaction_id ?? self::generateTransactionId();
        });
    }

    // Relationships
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function userPaymentMethod()
    {
        return $this->belongsTo(UserPaymentMethod::class);
    }

    public function refundedBy()
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRefunded($query)
    {
        return $query->whereIn('status', ['refunded', 'partially_refunded']);
    }

    // Generate unique transaction ID
    public static function generateTransactionId()
    {
        return 'TXN-' . strtoupper(Str::random(4)) . '-' . date('YmdHis') . '-' . rand(1000, 9999);
    }

    // Status helpers
    public function isSuccessful()
    {
        return $this->status === 'completed';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isRefunded()
    {
        return in_array($this->status, ['refunded', 'partially_refunded']);
    }

    public function canBeRefunded()
    {
        return $this->isSuccessful() && $this->getRefundableAmount() > 0;
    }

    // Get refundable amount
    public function getRefundableAmount()
    {
        return $this->amount - ($this->refunded_amount ?? 0);
    }

    // Get net amount (after fees)
    public function getNetAmountAttribute()
    {
        return $this->amount - ($this->fee ?? 0);
    }

    // Process refund
    public function processRefund($amount, $userId, $reason = null)
    {
        if ($amount > $this->getRefundableAmount()) {
            throw new \Exception('Refund amount exceeds refundable amount');
        }

        $totalRefunded = ($this->refunded_amount ?? 0) + $amount;
        $status = $totalRefunded >= $this->amount ? 'refunded' : 'partially_refunded';

        $this->update([
            'status' => $status,
            'refunded_amount' => $totalRefunded,
            'refunded_by' => $userId,
            'refunded_at' => now(),
            'refund_reason' => $reason,
        ]);

        return $this;
    }

    // Mark as completed
    public function markAsCompleted($providerReference = null)
    {
        $this->update([
            'status' => 'completed',
            'provider_reference' => $providerReference ?? $this->provider_reference,
        ]);

        return $this;
    }

    // Mark as failed
    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);

        return $this;
    }

    // Get status badge
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'completed' => 'success',
            'pending' => 'warning',
            'processing' => 'info',
            'failed' => 'danger',
            'refunded' => 'secondary',
            'partially_refunded' => 'info',
            'cancelled' => 'dark',
            default => 'secondary',
        };
    }

    // Get status icon
    public function getStatusIconAttribute()
    {
        return match($this->status) {
            'completed' => 'fa-check-circle',
            'pending' => 'fa-clock',
            'processing' => 'fa-spinner',
            'failed' => 'fa-times-circle',
            'refunded' => 'fa-undo',
            'partially_refunded' => 'fa-undo',
            'cancelled' => 'fa-ban',
            default => 'fa-question-circle',
        };
    }

    // Get formatted amount
    public function getFormattedAmountAttribute()
    {
        return '₹' . number_format($this->amount, 2);
    }
}
