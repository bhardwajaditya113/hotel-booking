<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add property_id to rooms
        Schema::table('rooms', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable()->after('id');
            $table->foreign('property_id')->references('id')->on('properties');
        });
        // Add property_id to bookings
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable()->after('rooms_id');
            $table->foreign('property_id')->references('id')->on('properties');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropColumn('property_id');
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropColumn('property_id');
        });
    }
};
