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
        // Notification Templates Table
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['email', 'sms', 'push', 'in_app']);
            $table->string('event'); // booking_confirmed, booking_cancelled, review_request, etc.
            
            $table->string('subject')->nullable(); // For email
            $table->text('body');
            $table->json('variables')->nullable(); // Available placeholders
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // User Notifications Table (enhanced)
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('type'); // booking, review, loyalty, promotion, system
            $table->string('title');
            $table->text('message');
            $table->string('action_url')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            
            $table->json('data')->nullable(); // Additional data
            
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'read_at']);
            $table->index(['type']);
        });

        // SMS Logs Table
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('phone_number');
            $table->string('template_id')->nullable();
            $table->text('message');
            $table->string('provider'); // twilio, msg91, etc.
            $table->string('provider_message_id')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->decimal('cost', 8, 4)->nullable();
            $table->timestamps();
            
            $table->index(['user_id']);
            $table->index(['status']);
        });

        // Push Notification Tokens Table
        Schema::create('push_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('token')->unique();
            $table->enum('platform', ['web', 'ios', 'android'])->default('web');
            $table->string('device_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
        });

        // Email Logs Table
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('to_email');
            $table->string('to_name')->nullable();
            $table->string('subject');
            $table->text('body')->nullable();
            $table->string('template')->nullable();
            $table->json('template_data')->nullable();
            
            $table->enum('status', ['pending', 'sent', 'opened', 'clicked', 'bounced', 'failed'])->default('pending');
            $table->string('provider_message_id')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->text('error_message')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id']);
            $table->index(['status']);
            $table->index(['to_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('push_tokens');
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('notification_templates');
    }
};
