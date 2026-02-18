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
        // Seasonal/Dynamic Pricing Rules Table
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('cascade');
            $table->foreignId('room_type_id')->nullable()->constrained('room_types')->onDelete('cascade');
            
            $table->string('name');
            $table->enum('rule_type', [
                'seasonal',      // Date range based
                'day_of_week',   // Specific days
                'occupancy',     // Based on occupancy rate
                'advance',       // Early bird or last minute
                'length_of_stay',// Min nights discount
                'special_event', // Holidays, events
                'demand'         // Dynamic demand-based
            ]);
            
            $table->enum('adjustment_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('adjustment_value', 10, 2); // Can be positive (increase) or negative (discount)
            
            // Date range for seasonal pricing
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            // Day of week (for weekend pricing) - JSON array of days [0-6]
            $table->json('days_of_week')->nullable();
            
            // Advance booking conditions
            $table->integer('min_days_advance')->nullable();
            $table->integer('max_days_advance')->nullable();
            
            // Length of stay conditions
            $table->integer('min_nights')->nullable();
            $table->integer('max_nights')->nullable();
            
            // Occupancy-based conditions
            $table->integer('occupancy_threshold')->nullable(); // percentage
            
            // Priority (higher = applied later)
            $table->integer('priority')->default(0);
            $table->boolean('is_stackable')->default(false); // Can combine with other rules
            $table->boolean('is_active')->default(true);
            
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['room_id', 'is_active']);
            $table->index(['room_type_id', 'is_active']);
            $table->index(['start_date', 'end_date']);
        });

        // Coupons/Promo Codes Table
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            
            $table->enum('type', ['percentage', 'fixed', 'free_night', 'free_breakfast']);
            $table->decimal('value', 10, 2);
            $table->decimal('max_discount', 10, 2)->nullable(); // Cap for percentage discounts
            $table->decimal('min_booking_amount', 10, 2)->nullable();
            $table->integer('min_nights')->nullable();
            
            // Usage limits
            $table->integer('total_uses')->nullable(); // null = unlimited
            $table->integer('uses_per_user')->default(1);
            $table->integer('times_used')->default(0);
            
            // Validity
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            
            // Restrictions
            $table->json('applicable_rooms')->nullable(); // Array of room IDs
            $table->json('applicable_room_types')->nullable(); // Array of room type IDs
            $table->foreignId('min_loyalty_tier_id')->nullable()->constrained('loyalty_tiers');
            $table->boolean('first_booking_only')->default(false);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['code', 'is_active']);
            $table->index(['valid_from', 'valid_until']);
        });

        // Coupon Usage Tracking Table
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('discount_amount', 10, 2);
            $table->timestamps();
            
            $table->index(['coupon_id', 'user_id']);
        });

        // Price History Table (for analytics)
        Schema::create('price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->date('date');
            $table->decimal('base_price', 10, 2);
            $table->decimal('final_price', 10, 2);
            $table->json('applied_rules')->nullable(); // Array of rule IDs that were applied
            $table->integer('occupancy_rate')->nullable();
            $table->timestamps();
            
            $table->unique(['room_id', 'date']);
            $table->index(['date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_history');
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('pricing_rules');
    }
};
