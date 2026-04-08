<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->unsignedSmallInteger('reorder_level')->default(1)->after('quantity');
            $table->unsignedSmallInteger('status')->default(1)->after('reorder_level')->comment('0: Inactive, 1: Active');
            $table->time('available_time_started_at')->nullable()->after('supplier_id');
            $table->time('available_time_ended_at')->nullable()->after('available_time_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['description', 'reorder_level', 'status', 'available_time_started_at', 'available_time_ended_at']);
        });
    }
};
