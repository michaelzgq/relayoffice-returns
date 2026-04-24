<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_cases', function (Blueprint $table) {
            $table->foreignId('expected_inbound_id')->nullable()->after('brand_rule_profile_id')->constrained('return_expected_inbounds')->nullOnDelete();
            $table->string('offline_draft_uuid')->nullable()->after('company_id')->index();
            $table->string('sync_status')->default('synced')->after('offline_draft_uuid');
            $table->text('sync_error')->nullable()->after('sync_status');
            $table->json('draft_payload')->nullable()->after('sync_error');
        });
    }

    public function down(): void
    {
        Schema::table('return_cases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('expected_inbound_id');
            $table->dropColumn([
                'offline_draft_uuid',
                'sync_status',
                'sync_error',
                'draft_payload',
            ]);
        });
    }
};
