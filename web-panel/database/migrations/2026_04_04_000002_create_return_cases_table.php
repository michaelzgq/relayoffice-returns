<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_cases', function (Blueprint $table) {
            $table->id();
            $table->string('return_id')->index();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_rule_profile_id')->nullable()->constrained('brand_rule_profiles')->nullOnDelete();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('product_sku')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('condition_code');
            $table->string('disposition_code');
            $table->string('inspection_status')->default('completed');
            $table->string('refund_status')->default('hold');
            $table->unsignedSmallInteger('required_photo_count')->default(0);
            $table->unsignedSmallInteger('evidence_photo_count')->default(0);
            $table->boolean('evidence_complete')->default(false);
            $table->unsignedSmallInteger('sla_hours')->default(24);
            $table->text('notes')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('inspected_at')->nullable();
            $table->timestamp('refund_decided_at')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_cases');
    }
};
