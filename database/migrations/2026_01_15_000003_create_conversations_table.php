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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user1_id'); // Guest or first participant
            $table->unsignedBigInteger('user2_id'); // Host or second participant
            $table->unsignedBigInteger('property_id')->nullable(); // Related property
            $table->unsignedBigInteger('booking_id')->nullable(); // Related booking
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('user1_archived')->default(false);
            $table->boolean('user2_archived')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user1_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user2_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');

            // Ensure unique conversation between two users for a property
            $table->unique(['user1_id', 'user2_id', 'property_id']);
            $table->index(['user1_id', 'last_message_at']);
            $table->index(['user2_id', 'last_message_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};

