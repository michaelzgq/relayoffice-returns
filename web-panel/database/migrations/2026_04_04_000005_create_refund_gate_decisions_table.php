<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_gate_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_case_id')->constrained('return_cases')->cascadeOnDelete();
            $table->string('status');
            $table->text('reason')->nullable();
            $table->json('meta')->nullable();
            $table->unsignedBigInteger('decided_by')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->unique('return_case_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_gate_decisions');
    }
};
