<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            if (! Schema::hasColumn('tags', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('is_active');
            }
            if (! Schema::hasColumn('tags', 'color')) {
                $table->string('color', 32)->nullable()->after('slug');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            if (Schema::hasColumn('tags', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
            if (Schema::hasColumn('tags', 'color')) {
                $table->dropColumn('color');
            }
        });
    }
};
