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
        Schema::table('properties', function (Blueprint $table) {
            // Listing type: 'hotel' (OYO style) or 'unique_stay' (Airbnb style)
            $table->enum('listing_type', ['hotel', 'unique_stay'])->default('hotel')->after('property_type_id');
            
            // Verification status
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending')->after('status');
            $table->timestamp('verified_at')->nullable()->after('verification_status');
            $table->text('verification_notes')->nullable()->after('verified_at');
            
            // Location coordinates for map view
            $table->decimal('latitude', 10, 8)->nullable()->after('zipcode');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            
            // Property-level settings
            $table->boolean('instant_book_enabled')->default(true)->after('longitude');
            $table->time('check_in_time')->default('14:00')->after('instant_book_enabled');
            $table->time('check_out_time')->default('11:00')->after('check_in_time');
            
            // Property-level amenities (JSON array of amenity IDs)
            $table->json('amenities')->nullable()->after('check_out_time');
            
            // Property images gallery
            $table->json('images')->nullable()->after('amenities');
            $table->string('cover_image')->nullable()->after('images');
            
            // Property rules and policies
            $table->text('house_rules')->nullable()->after('cover_image');
            $table->text('cancellation_policy_text')->nullable()->after('house_rules');
            
            // SEO and marketing
            $table->string('slug')->nullable()->unique()->after('cancellation_policy_text');
            $table->string('meta_title')->nullable()->after('slug');
            $table->text('meta_description')->nullable()->after('meta_title');
            
            // Statistics (cached for performance)
            $table->decimal('average_rating', 3, 2)->nullable()->after('meta_description');
            $table->integer('total_reviews')->default(0)->after('average_rating');
            $table->integer('total_bookings')->default(0)->after('total_reviews');
            $table->integer('view_count')->default(0)->after('total_bookings');
            
            // Featured and promoted
            $table->boolean('is_featured')->default(false)->after('view_count');
            $table->boolean('is_promoted')->default(false)->after('is_featured');
            $table->timestamp('promoted_until')->nullable()->after('is_promoted');
            
            // Soft delete
            $table->softDeletes();
            
            // Indexes for search performance
            $table->index(['listing_type', 'status', 'verification_status']);
            $table->index(['city', 'state', 'country']);
            $table->index(['average_rating', 'total_reviews']);
            $table->index(['is_featured', 'is_promoted']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['listing_type', 'status', 'verification_status']);
            $table->dropIndex(['city', 'state', 'country']);
            $table->dropIndex(['average_rating', 'total_reviews']);
            $table->dropIndex(['is_featured', 'is_promoted']);
            
            $table->dropColumn([
                'listing_type',
                'verification_status',
                'verified_at',
                'verification_notes',
                'latitude',
                'longitude',
                'instant_book_enabled',
                'check_in_time',
                'check_out_time',
                'amenities',
                'images',
                'cover_image',
                'house_rules',
                'cancellation_policy_text',
                'slug',
                'meta_title',
                'meta_description',
                'average_rating',
                'total_reviews',
                'total_bookings',
                'view_count',
                'is_featured',
                'is_promoted',
                'promoted_until',
                'deleted_at'
            ]);
        });
    }
};


