<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('host_profiles', function (Blueprint $table) {
            $table->string('razorpay_linked_account_id')->nullable()->after('payout_email');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('razorpay_order_id')->nullable()->after('transation_id');
            $table->decimal('marketplace_platform_fee_base_inr', 14, 2)->nullable()->after('total_price');
            $table->decimal('marketplace_platform_gst_inr', 14, 2)->nullable()->after('marketplace_platform_fee_base_inr');
            $table->decimal('marketplace_platform_total_inr', 14, 2)->nullable()->after('marketplace_platform_gst_inr');
            $table->decimal('marketplace_host_payout_inr', 14, 2)->nullable()->after('marketplace_platform_total_inr');
            $table->string('marketplace_settlement_status', 32)->nullable()->after('marketplace_host_payout_inr');
            $table->json('marketplace_transfer_ids')->nullable()->after('marketplace_settlement_status');
            $table->string('marketplace_dispute_id')->nullable()->after('marketplace_transfer_ids');
            $table->boolean('marketplace_route_transfer_used')->default(false)->after('marketplace_dispute_id');

            $table->index(['marketplace_settlement_status']);
            $table->index(['razorpay_order_id']);
        });
    }

    public function down(): void
    {
        Schema::table('host_profiles', function (Blueprint $table) {
            $table->dropColumn('razorpay_linked_account_id');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['marketplace_settlement_status']);
            $table->dropIndex(['razorpay_order_id']);
            $table->dropColumn([
                'razorpay_order_id',
                'marketplace_platform_fee_base_inr',
                'marketplace_platform_gst_inr',
                'marketplace_platform_total_inr',
                'marketplace_host_payout_inr',
                'marketplace_settlement_status',
                'marketplace_transfer_ids',
                'marketplace_dispute_id',
                'marketplace_route_transfer_used',
            ]);
        });
    }
};
