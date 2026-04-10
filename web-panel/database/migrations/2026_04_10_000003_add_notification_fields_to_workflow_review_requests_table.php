<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workflow_review_requests', function (Blueprint $table) {
            $table->string('notification_status')->default('pending')->after('status')->index();
            $table->timestamp('notification_attempted_at')->nullable()->after('reviewed_at');
            $table->timestamp('notification_sent_at')->nullable()->after('notification_attempted_at');
            $table->text('notification_error')->nullable()->after('notification_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('workflow_review_requests', function (Blueprint $table) {
            $table->dropColumn([
                'notification_status',
                'notification_attempted_at',
                'notification_sent_at',
                'notification_error',
            ]);
        });
    }
};
