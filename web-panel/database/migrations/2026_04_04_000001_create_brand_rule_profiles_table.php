<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_rule_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('profile_name');
            $table->json('allowed_conditions');
            $table->json('allowed_dispositions');
            $table->json('required_photo_types')->nullable();
            $table->unsignedSmallInteger('required_photo_count')->default(3);
            $table->boolean('notes_required')->default(true);
            $table->boolean('sku_required')->default(false);
            $table->boolean('serial_required')->default(false);
            $table->string('default_refund_status')->default('hold');
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('company_id')->nullable();
            $table->timestamps();

            $table->unique('brand_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_rule_profiles');
    }
};
