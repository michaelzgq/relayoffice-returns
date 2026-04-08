<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->string('admin_payment_method_id')->after('refund_reason');
            $table->string('admin_payment_method_name')->after('admin_payment_method_id');
            $table->string('customer_payout_method_name')->after('admin_payment_method_name');
            $table->json('other_payment_details')->nullable()->after('customer_payout_method_name');
        });
    }

    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->dropColumn(['admin_payment_method_id', 'admin_payment_method_name', 'customer_payout_method_name', 'other_payment_details']);
        });
    }
};
