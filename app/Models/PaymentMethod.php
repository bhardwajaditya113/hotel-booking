<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'config' => 'encrypted:array',
        'supported_currencies' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    protected $hidden = ['config'];

    // Relationships
    public function userPaymentMethods()
    {
        return $this->hasMany(UserPaymentMethod::class);
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Get default method
    public static function getDefault()
    {
        return self::active()->where('is_default', true)->first()
            ?? self::active()->ordered()->first();
    }

    // Check if amount is within limits
    public function isAmountValid($amount)
    {
        if ($this->min_amount && $amount < $this->min_amount) return false;
        if ($this->max_amount && $amount > $this->max_amount) return false;
        return true;
    }

    // Calculate transaction fee
    public function calculateFee($amount)
    {
        $percentageFee = $amount * ($this->transaction_fee / 100);
        return round($percentageFee + $this->fixed_fee, 2);
    }

    // Check if currency is supported
    public function supportsCurrency($currency)
    {
        if (empty($this->supported_currencies)) return true;
        return in_array($currency, $this->supported_currencies);
    }

    // Create default payment methods
    public static function createDefaults()
    {
        $methods = [
            [
                'name' => 'Credit/Debit Card (Stripe)',
                'slug' => 'stripe',
                'provider' => 'stripe',
                'icon' => 'fa-credit-card',
                'description' => 'Pay securely with Visa, Mastercard, or American Express',
                'supported_currencies' => ['INR', 'USD', 'EUR', 'GBP'],
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
                'transaction_fee' => 2.9,
                'fixed_fee' => 0.30,
            ],
            [
                'name' => 'Razorpay',
                'slug' => 'razorpay',
                'provider' => 'razorpay',
                'icon' => 'fa-indian-rupee-sign',
                'description' => 'Pay with UPI, Cards, Net Banking, Wallets',
                'supported_currencies' => ['INR'],
                'is_active' => true,
                'sort_order' => 2,
                'transaction_fee' => 2.0,
                'fixed_fee' => 0,
            ],
            [
                'name' => 'PayPal',
                'slug' => 'paypal',
                'provider' => 'paypal',
                'icon' => 'fa-paypal',
                'description' => 'Pay with your PayPal account',
                'supported_currencies' => ['USD', 'EUR', 'GBP'],
                'is_active' => false,
                'sort_order' => 3,
                'transaction_fee' => 3.49,
                'fixed_fee' => 0.49,
            ],
            [
                'name' => 'Wallet',
                'slug' => 'wallet',
                'provider' => 'wallet',
                'icon' => 'fa-wallet',
                'description' => 'Pay using your wallet balance',
                'supported_currencies' => ['INR'],
                'is_active' => true,
                'sort_order' => 4,
                'transaction_fee' => 0,
                'fixed_fee' => 0,
            ],
            [
                'name' => 'Pay at Hotel',
                'slug' => 'pay_at_hotel',
                'provider' => 'cash',
                'icon' => 'fa-hotel',
                'description' => 'Pay when you arrive at the property',
                'supported_currencies' => ['INR'],
                'is_active' => true,
                'sort_order' => 5,
                'transaction_fee' => 0,
                'fixed_fee' => 0,
            ],
        ];

        foreach ($methods as $method) {
            self::updateOrCreate(['slug' => $method['slug']], $method);
        }
    }
}
