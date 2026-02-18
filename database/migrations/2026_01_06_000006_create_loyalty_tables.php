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
        // Loyalty Tiers Table
        Schema::create('loyalty_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Bronze, Silver, Gold, Platinum
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('color')->nullable(); // Hex color
            $table->integer('min_points')->default(0);
            $table->integer('min_bookings')->default(0);
            $table->integer('points_multiplier')->default(100); // 100 = 1x, 150 = 1.5x
            $table->integer('discount_percentage')->default(0);
            $table->json('benefits')->nullable(); // JSON array of benefits
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // User Loyalty Table
        Schema::create('user_loyalty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('loyalty_tier_id')->constrained()->onDelete('restrict');
            $table->integer('total_points')->default(0);
            $table->integer('available_points')->default(0);
            $table->integer('lifetime_points')->default(0);
            $table->integer('total_bookings')->default(0);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->timestamp('tier_upgraded_at')->nullable();
            $table->timestamp('tier_expires_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id']);
            $table->index(['loyalty_tier_id']);
        });

        // Points Transactions Table
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            
            $table->enum('type', ['earn', 'redeem', 'expire', 'bonus', 'adjustment', 'referral']);
            $table->integer('points');
            $table->integer('balance_after');
            
            $table->string('description');
            $table->string('reference_type')->nullable(); // booking, referral, promotion, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
            $table->index(['expires_at']);
        });

        // Rewards Catalog Table
        Schema::create('loyalty_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image')->nullable();
            
            $table->enum('type', ['discount', 'free_night', 'upgrade', 'amenity', 'experience', 'voucher']);
            $table->integer('points_required');
            $table->decimal('value', 10, 2)->nullable(); // Monetary value
            $table->integer('discount_percentage')->nullable();
            
            $table->foreignId('min_tier_id')->nullable()->constrained('loyalty_tiers');
            $table->integer('quantity_available')->nullable(); // null = unlimited
            $table->integer('quantity_redeemed')->default(0);
            
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->json('conditions')->nullable(); // JSON with conditions
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // User Redeemed Rewards Table
        Schema::create('user_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('loyalty_reward_id')->constrained()->onDelete('restrict');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('loyalty_transaction_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('code')->unique(); // Unique redemption code
            $table->enum('status', ['active', 'used', 'expired', 'cancelled'])->default('active');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['code']);
        });

        // Referral Program Table
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('referral_code')->unique();
            $table->string('referred_email')->nullable();
            $table->enum('status', ['pending', 'registered', 'booked', 'completed', 'expired'])->default('pending');
            
            $table->integer('referrer_points')->default(0);
            $table->integer('referred_points')->default(0);
            $table->decimal('referrer_credit', 10, 2)->default(0);
            $table->decimal('referred_credit', 10, 2)->default(0);
            
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('first_booking_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['referrer_id']);
            $table->index(['referral_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('user_rewards');
        Schema::dropIfExists('loyalty_rewards');
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('user_loyalty');
        Schema::dropIfExists('loyalty_tiers');
    }
};
