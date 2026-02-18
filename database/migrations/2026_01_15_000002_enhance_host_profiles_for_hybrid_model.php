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
        Schema::table('host_profiles', function (Blueprint $table) {
            // Enhanced host type: individual, company, hotel_chain
            // Note: Using string instead of enum for compatibility
            $table->string('type')->default('individual')->change();
            
            // Company/Hotel details (for OYO-style listings)
            $table->string('company_name')->nullable()->after('type');
            $table->string('company_registration_number')->nullable()->after('company_name');
            $table->string('company_tax_id')->nullable()->after('company_registration_number');
            $table->text('company_address')->nullable()->after('company_tax_id');
            $table->string('company_website')->nullable()->after('company_address');
            
            // Verification
            $table->string('verification_status')->default('pending')->after('company_website');
            $table->timestamp('verified_at')->nullable()->after('verification_status');
            $table->text('verification_notes')->nullable()->after('verified_at');
            
            // Identity verification (for individual hosts)
            $table->string('identity_document_type')->nullable()->after('verification_notes');
            $table->string('identity_document_number')->nullable()->after('identity_document_type');
            $table->string('identity_document_file')->nullable()->after('identity_document_number');
            
            // Host statistics
            $table->integer('total_properties')->default(0)->after('identity_document_file');
            $table->integer('total_bookings')->default(0)->after('total_properties');
            $table->decimal('average_rating', 3, 2)->nullable()->after('total_bookings');
            $table->integer('total_reviews')->default(0)->after('average_rating');
            
            // Response time and rate (for Airbnb-style hosts)
            $table->integer('average_response_time_minutes')->nullable()->after('total_reviews');
            $table->decimal('response_rate', 5, 2)->nullable()->after('average_response_time_minutes');
            
            // Superhost status (Airbnb-style)
            $table->boolean('is_superhost')->default(false)->after('response_rate');
            $table->timestamp('superhost_since')->nullable()->after('is_superhost');
            
            // Languages spoken
            $table->json('languages_spoken')->nullable()->after('superhost_since');
            
            // Social media links
            $table->string('facebook_url')->nullable()->after('languages_spoken');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('twitter_url')->nullable()->after('instagram_url');
            $table->string('linkedin_url')->nullable()->after('twitter_url');
            
            // Bank account details for payouts (encrypted in production)
            $table->string('bank_account_name')->nullable()->after('linkedin_url');
            $table->string('bank_account_number')->nullable()->after('bank_account_name');
            $table->string('bank_ifsc_code')->nullable()->after('bank_account_number');
            $table->string('bank_name')->nullable()->after('bank_ifsc_code');
            
            // Payment preferences
            $table->string('payout_method')->default('bank_transfer')->after('bank_name');
            $table->string('payout_email')->nullable()->after('payout_method');
            
            $table->softDeletes();
            
            // Indexes
            $table->index(['type', 'verification_status']);
            $table->index(['is_superhost', 'average_rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('host_profiles', function (Blueprint $table) {
            $table->dropIndex(['type', 'verification_status']);
            $table->dropIndex(['is_superhost', 'average_rating']);
            
            $table->dropColumn([
                'company_name',
                'company_registration_number',
                'company_tax_id',
                'company_address',
                'company_website',
                'verification_status',
                'verified_at',
                'verification_notes',
                'identity_document_type',
                'identity_document_number',
                'identity_document_file',
                'total_properties',
                'total_bookings',
                'average_rating',
                'total_reviews',
                'average_response_time_minutes',
                'response_rate',
                'is_superhost',
                'superhost_since',
                'languages_spoken',
                'facebook_url',
                'instagram_url',
                'twitter_url',
                'linkedin_url',
                'bank_account_name',
                'bank_account_number',
                'bank_ifsc_code',
                'bank_name',
                'payout_method',
                'payout_email',
                'deleted_at'
            ]);
        });
    }
};

