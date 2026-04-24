<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_expected_inbounds', function (Blueprint $table) {
            $table->id();
            $table->string('return_id')->index();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('product_sku')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('tracking_number')->nullable()->index();
            $table->string('return_reason')->nullable();
            $table->string('expected_condition')->nullable();
            $table->string('source')->default('csv');
            $table->string('status')->default('pending')->index();
            $table->foreignId('matched_return_case_id')->nullable()->constrained('return_cases')->nullOnDelete();
            $table->unsignedBigInteger('imported_by')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->unique(['brand_id', 'return_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_expected_inbounds');
    }
};
