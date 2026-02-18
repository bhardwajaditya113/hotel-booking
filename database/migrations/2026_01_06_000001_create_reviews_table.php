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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('cascade');
            
            // Multi-dimensional ratings (1-5 scale)
            $table->tinyInteger('rating_overall')->default(5);
            $table->tinyInteger('rating_cleanliness')->default(5);
            $table->tinyInteger('rating_location')->default(5);
            $table->tinyInteger('rating_service')->default(5);
            $table->tinyInteger('rating_value')->default(5);
            $table->tinyInteger('rating_amenities')->default(5);
            $table->tinyInteger('rating_comfort')->default(5);
            
            $table->text('review_text')->nullable();
            $table->text('pros')->nullable();
            $table->text('cons')->nullable();
            
            // Host/Admin response
            $table->text('owner_response')->nullable();
            $table->timestamp('owner_response_at')->nullable();
            
            // Review metadata
            $table->enum('trip_type', ['business', 'leisure', 'family', 'couple', 'solo', 'friends'])->nullable();
            $table->boolean('is_verified')->default(true); // Verified stay
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->integer('helpful_count')->default(0);
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['room_id', 'is_approved']);
            $table->index(['user_id']);
            $table->index(['rating_overall']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
