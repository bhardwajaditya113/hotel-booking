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
        // Add new columns to rooms table for enhanced search
        Schema::table('rooms', function (Blueprint $table) {
            // Cancellation policy
            $table->foreignId('cancellation_policy_id')->nullable()->after('status')->constrained();
            
            // Rating aggregates (cached for performance)
            $table->decimal('average_rating', 3, 2)->nullable()->after('discount');
            $table->integer('total_reviews')->default(0)->after('average_rating');
            
            // Additional searchable attributes
            $table->boolean('instant_booking')->default(true)->after('total_reviews');
            $table->boolean('free_cancellation')->default(false)->after('instant_booking');
            $table->boolean('breakfast_included')->default(false)->after('free_cancellation');
            $table->boolean('wifi_included')->default(true)->after('breakfast_included');
            $table->boolean('parking_available')->default(false)->after('wifi_included');
            $table->boolean('pet_friendly')->default(false)->after('parking_available');
            $table->boolean('smoking_allowed')->default(false)->after('pet_friendly');
            
            // Property details
            $table->integer('floor_number')->nullable()->after('smoking_allowed');
            $table->decimal('room_size_sqft', 8, 2)->nullable()->after('floor_number');
            $table->string('check_in_time')->default('14:00')->after('room_size_sqft');
            $table->string('check_out_time')->default('11:00')->after('check_in_time');
            
            // For popularity sorting
            $table->integer('booking_count')->default(0)->after('check_out_time');
            $table->integer('view_count')->default(0)->after('booking_count');
            
            // SEO
            $table->string('meta_title')->nullable()->after('description');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('slug')->nullable()->unique()->after('meta_description');
            
            // Soft delete
            $table->softDeletes();
            
            // Indexes for search
            $table->index(['status', 'average_rating']);
            $table->index(['price']);
            $table->index(['booking_count']);
        });

        // Add columns to bookings table
        Schema::table('bookings', function (Blueprint $table) {
            // Pricing breakdown
            $table->decimal('base_price', 10, 2)->nullable()->after('total_price');
            $table->decimal('taxes', 10, 2)->default(0)->after('base_price');
            $table->decimal('service_fee', 10, 2)->default(0)->after('taxes');
            $table->decimal('cleaning_fee', 10, 2)->default(0)->after('service_fee');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('cleaning_fee');
            $table->string('coupon_code')->nullable()->after('discount_amount');
            $table->integer('loyalty_points_used')->default(0)->after('coupon_code');
            $table->decimal('loyalty_discount', 10, 2)->default(0)->after('loyalty_points_used');
            $table->decimal('wallet_used', 10, 2)->default(0)->after('loyalty_discount');
            
            // Enhanced status
            $table->enum('booking_status', [
                'pending',
                'confirmed',
                'checked_in',
                'checked_out',
                'completed',
                'cancelled',
                'no_show',
                'refunded'
            ])->default('pending')->after('status');
            
            // Guest details
            $table->text('special_requests')->nullable()->after('address');
            $table->time('expected_arrival_time')->nullable()->after('special_requests');
            $table->string('purpose_of_visit')->nullable()->after('expected_arrival_time');
            
            // Review tracking
            $table->boolean('review_requested')->default(false)->after('purpose_of_visit');
            $table->timestamp('review_requested_at')->nullable()->after('review_requested');
            
            // Points earned
            $table->integer('loyalty_points_earned')->default(0)->after('review_requested_at');
            
            $table->softDeletes();
        });

        // Add columns to users table
        Schema::table('users', function (Blueprint $table) {
            // Profile enhancements
            $table->date('date_of_birth')->nullable()->after('address');
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('date_of_birth');
            $table->string('nationality')->nullable()->after('gender');
            $table->string('language_preference')->default('en')->after('nationality');
            $table->string('currency_preference')->default('INR')->after('language_preference');
            
            // Verification
            $table->boolean('phone_verified')->default(false)->after('currency_preference');
            $table->boolean('email_verified_flag')->default(false)->after('phone_verified');
            $table->boolean('identity_verified')->default(false)->after('email_verified_flag');
            $table->string('identity_document_type')->nullable()->after('identity_verified');
            $table->string('identity_document_number')->nullable()->after('identity_document_type');
            
            // Referral
            $table->string('referral_code')->nullable()->unique()->after('identity_document_number');
            $table->foreignId('referred_by')->nullable()->after('referral_code')->constrained('users')->onDelete('set null');
            
            // Communication preferences
            $table->json('notification_preferences')->nullable()->after('referred_by');
            
            // Membership date
            $table->timestamp('last_login_at')->nullable()->after('notification_preferences');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['cancellation_policy_id']);
            $table->dropColumn([
                'cancellation_policy_id', 'average_rating', 'total_reviews',
                'instant_booking', 'free_cancellation', 'breakfast_included',
                'wifi_included', 'parking_available', 'pet_friendly', 'smoking_allowed',
                'floor_number', 'room_size_sqft', 'check_in_time', 'check_out_time',
                'booking_count', 'view_count', 'meta_title', 'meta_description', 'slug',
                'deleted_at'
            ]);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'base_price', 'taxes', 'service_fee', 'cleaning_fee',
                'discount_amount', 'coupon_code', 'loyalty_points_used',
                'loyalty_discount', 'wallet_used', 'booking_status',
                'special_requests', 'expected_arrival_time', 'purpose_of_visit',
                'review_requested', 'review_requested_at', 'loyalty_points_earned',
                'deleted_at'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn([
                'date_of_birth', 'gender', 'nationality',
                'language_preference', 'currency_preference',
                'phone_verified', 'email_verified_flag', 'identity_verified',
                'identity_document_type', 'identity_document_number',
                'referral_code', 'referred_by', 'notification_preferences',
                'last_login_at', 'last_login_ip', 'deleted_at'
            ]);
        });
    }
};
