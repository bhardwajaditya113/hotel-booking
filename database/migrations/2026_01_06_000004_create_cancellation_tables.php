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
        // Cancellation Policies Table
        Schema::create('cancellation_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Flexible, Moderate, Strict, Non-Refundable
            $table->string('slug')->unique();
            $table->text('description');
            $table->json('rules'); // JSON with refund rules based on time before check-in
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Booking Cancellations Table
        Schema::create('booking_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cancellation_policy_id')->nullable()->constrained();
            
            $table->enum('cancelled_by', ['guest', 'host', 'admin', 'system'])->default('guest');
            $table->enum('reason_type', [
                'change_of_plans',
                'found_alternative',
                'emergency',
                'weather',
                'health',
                'work',
                'travel_restrictions',
                'property_issue',
                'other'
            ])->nullable();
            $table->text('reason_details')->nullable();
            
            // Financial details
            $table->decimal('original_amount', 10, 2);
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->decimal('cancellation_fee', 10, 2)->default(0);
            $table->integer('refund_percentage')->default(0);
            
            // Refund status
            $table->enum('refund_status', ['pending', 'processing', 'completed', 'failed', 'none'])->default('pending');
            $table->string('refund_method')->nullable(); // original_payment, wallet, bank_transfer
            $table->string('refund_transaction_id')->nullable();
            $table->timestamp('refund_processed_at')->nullable();
            
            // Admin notes
            $table->text('admin_notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['booking_id']);
            $table->index(['refund_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_cancellations');
        Schema::dropIfExists('cancellation_policies');
    }
};
