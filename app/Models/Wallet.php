<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // Get or create wallet for user
    public static function getOrCreate($userId, $currency = 'INR')
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0, 'currency' => $currency, 'is_active' => true]
        );
    }

    // Credit money to wallet
    public function credit($amount, $transactionType, $description, $bookingId = null, $expiresAt = null, $metadata = null)
    {
        if ($amount <= 0) return null;

        $balanceBefore = $this->balance;
        $this->increment('balance', $amount);

        return WalletTransaction::create([
            'wallet_id' => $this->id,
            'user_id' => $this->user_id,
            'booking_id' => $bookingId,
            'type' => 'credit',
            'transaction_type' => $transactionType,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => $description,
            'expires_at' => $expiresAt,
            'metadata' => $metadata,
        ]);
    }

    // Debit money from wallet
    public function debit($amount, $transactionType, $description, $bookingId = null, $metadata = null)
    {
        if ($amount <= 0) return null;
        if ($this->balance < $amount) return null;

        $balanceBefore = $this->balance;
        $this->decrement('balance', $amount);

        return WalletTransaction::create([
            'wallet_id' => $this->id,
            'user_id' => $this->user_id,
            'booking_id' => $bookingId,
            'type' => 'debit',
            'transaction_type' => $transactionType,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    // Check if wallet has sufficient balance
    public function hasSufficientBalance($amount)
    {
        return $this->is_active && $this->balance >= $amount;
    }

    // Get formatted balance
    public function getFormattedBalanceAttribute()
    {
        return '₹' . number_format($this->balance, 2);
    }

    // Get available cashback (non-expired credits)
    public function getAvailableCashbackAttribute()
    {
        // This is simplified; in production, you'd track cashback separately
        return $this->transactions()
            ->where('type', 'credit')
            ->where('transaction_type', 'cashback')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->sum('amount');
    }

    // Process expired cashback
    public function processExpiredCashback()
    {
        $expired = $this->transactions()
            ->where('type', 'credit')
            ->where('transaction_type', 'cashback')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expired as $transaction) {
            // Debit expired amount
            $this->debit(
                $transaction->amount,
                'expired',
                "Expired cashback from {$transaction->created_at->format('M d, Y')}"
            );
        }

        return $expired->count();
    }
}
