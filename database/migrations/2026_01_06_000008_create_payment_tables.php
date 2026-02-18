<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // User Wallet Table
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 12, 2)->default(0);
            $table->string('currency', 3)->default('INR');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['user_id']);
        });

        // Wallet Transactions Table
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            
            $table->enum('type', ['credit', 'debit']);
            $table->enum('transaction_type', [
                'deposit',      // User adds money
                'refund',       // Booking cancellation refund
                'payment',      // Used for booking payment
                'cashback',     // Promotional cashback
                'reward',       // Loyalty reward
                'referral',     // Referral bonus
                'adjustment',   // Admin adjustment
                'withdrawal',   // Withdraw to bank
                'expired'       // Expired cashback
            ]);
            
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 12, 2);
            $table->decimal('balance_after', 12, 2);
            
            $table->string('description');
            $table->string('reference_id')->nullable();
            $table->json('metadata')->nullable();
            
            $table->timestamp('expires_at')->nullable(); // For cashback with expiry
            $table->timestamps();
            
            $table->index(['wallet_id', 'type']);
            $table->index(['user_id']);
        });

        // Payment Methods Table
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('provider'); // stripe, razorpay, paypal, wallet, bank_transfer
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            
            $table->json('config')->nullable(); // API keys and settings (encrypted)
            $table->json('supported_currencies')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->decimal('transaction_fee', 5, 2)->default(0); // Percentage
            $table->decimal('fixed_fee', 10, 2)->default(0);
            
            $table->timestamps();
        });

        // User Saved Payment Methods Table
        Schema::create('user_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
            
            $table->string('type'); // card, upi, bank_account
            $table->string('provider_customer_id')->nullable();
            $table->string('provider_method_id')->nullable();
            
            // Card details (masked)
            $table->string('card_brand')->nullable(); // visa, mastercard, etc.
            $table->string('card_last_four')->nullable();
            $table->string('card_expiry')->nullable();
            
            // UPI details
            $table->string('upi_id')->nullable();
            
            // Bank details
            $table->string('bank_name')->nullable();
            $table->string('account_last_four')->nullable();
            
            $table->string('nickname')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            
            $table->index(['user_id']);
        });

        // Payment Transactions Table
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->nullable()->constrained();
            
            $table->string('transaction_id')->unique();
            $table->string('provider_transaction_id')->nullable();
            $table->string('provider_order_id')->nullable();
            
            $table->enum('type', ['payment', 'refund', 'partial_refund']);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded']);
            
            $table->decimal('amount', 12, 2);
            $table->decimal('fee', 10, 2)->default(0);
            $table->decimal('net_amount', 12, 2);
            $table->string('currency', 3)->default('INR');
            
            // For partial payments
            $table->decimal('wallet_amount', 12, 2)->default(0);
            $table->decimal('gateway_amount', 12, 2)->default(0);
            $table->decimal('points_amount', 12, 2)->default(0);
            
            $table->json('provider_response')->nullable();
            $table->text('failure_reason')->nullable();
            
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['booking_id']);
            $table->index(['user_id']);
            $table->index(['status']);
            $table->index(['provider_transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('user_payment_methods');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};
