<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingCancellation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'refund_processed_at' => 'datetime',
    ];

    // Relationships
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cancellationPolicy()
    {
        return $this->belongsTo(CancellationPolicy::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('refund_status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('refund_status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('refund_status', 'completed');
    }

    // Status helpers
    public function isPending()
    {
        return $this->refund_status === 'pending';
    }

    public function isCompleted()
    {
        return $this->refund_status === 'completed';
    }

    // Process refund
    public function processRefund($adminId = null, $method = 'wallet')
    {
        $this->update([
            'refund_status' => 'completed',
            'refund_method' => $method,
            'refund_processed_at' => now(),
            'processed_by' => $adminId,
        ]);

        // If refunding to wallet
        if ($method === 'wallet' && $this->refund_amount > 0) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $this->user_id],
                ['balance' => 0, 'currency' => 'INR']
            );
            
            $wallet->credit(
                $this->refund_amount,
                'refund',
                "Refund for booking #{$this->booking_id}",
                $this->booking_id
            );
        }

        return $this;
    }

    // Get reason display text
    public function getReasonDisplayAttribute()
    {
        $reasons = [
            'change_of_plans' => 'Change of plans',
            'found_alternative' => 'Found alternative accommodation',
            'emergency' => 'Personal emergency',
            'weather' => 'Weather conditions',
            'health' => 'Health issues',
            'work' => 'Work-related',
            'travel_restrictions' => 'Travel restrictions',
            'property_issue' => 'Issue with property',
            'other' => 'Other reason',
        ];

        return $reasons[$this->reason_type] ?? 'Not specified';
    }
}
