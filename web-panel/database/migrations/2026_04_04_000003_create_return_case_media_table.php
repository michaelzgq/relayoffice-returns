<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_case_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_case_id')->constrained('return_cases')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('media_type')->default('image');
            $table->string('capture_type')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_case_media');
    }
};
