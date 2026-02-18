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
        // Amenities/Facilities Categories Table
        Schema::create('amenity_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Amenities Table (enhanced)
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('amenity_categories')->onDelete('set null');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable(); // Icon class or SVG
            $table->text('description')->nullable();
            $table->boolean('is_highlighted')->default(false); // Show in search filters
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Room Amenities Pivot Table
        Schema::create('room_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('amenity_id')->constrained('amenities')->onDelete('cascade');
            $table->text('notes')->nullable(); // e.g., "24 hours", "Paid", etc.
            $table->boolean('is_paid')->default(false);
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['room_id', 'amenity_id']);
        });

        // Property Tags Table (for search/filtering)
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('general'); // general, location, style, feature
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Room Tags Pivot Table
        Schema::create('room_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['room_id', 'tag_id']);
        });

        // Nearby Places Table
        Schema::create('nearby_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['restaurant', 'attraction', 'transport', 'shopping', 'beach', 'park', 'hospital', 'airport', 'other']);
            $table->decimal('distance', 8, 2); // in km
            $table->string('distance_unit')->default('km');
            $table->integer('travel_time_minutes')->nullable();
            $table->string('travel_mode')->nullable(); // walking, driving, public_transport
            $table->timestamps();
        });

        // House Rules Table
        Schema::create('house_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->string('rule');
            $table->string('icon')->nullable();
            $table->enum('type', ['allowed', 'not_allowed', 'info'])->default('info');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('house_rules');
        Schema::dropIfExists('nearby_places');
        Schema::dropIfExists('room_tags');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('room_amenities');
        Schema::dropIfExists('amenities');
        Schema::dropIfExists('amenity_categories');
    }
};
