<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_review_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('work_email')->index();
            $table->string('company_name');
            $table->string('role_title')->nullable();
            $table->string('volume_note')->nullable();
            $table->text('workflow_note');
            $table->string('submitted_from_host')->nullable();
            $table->string('submitted_from_url')->nullable();
            $table->string('status')->default('new')->index();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_review_requests');
    }
};
