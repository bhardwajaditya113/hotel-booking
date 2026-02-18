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
        // Wishlists/Collections Table
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->default('My Favorites');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('privacy', ['private', 'public', 'shared'])->default('private');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index(['user_id']);
        });

        // Wishlist Items Table
        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wishlist_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->date('planned_check_in')->nullable();
            $table->date('planned_check_out')->nullable();
            $table->timestamps();
            
            $table->unique(['wishlist_id', 'room_id']);
            $table->index(['room_id']);
        });

        // Wishlist Shares (for collaborative wishlists)
        Schema::create('wishlist_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wishlist_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('permission', ['view', 'edit'])->default('view');
            $table->string('invite_token')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
            
            $table->unique(['wishlist_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_shares');
        Schema::dropIfExists('wishlist_items');
        Schema::dropIfExists('wishlists');
    }
};
