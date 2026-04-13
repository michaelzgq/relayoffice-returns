<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_click_events', function (Blueprint $table) {
            $table->id();
            $table->string('page_key', 60)->index();
            $table->string('placement', 60)->nullable()->index();
            $table->string('cta_key', 60)->index();
            $table->string('cta_label', 160)->nullable();
            $table->string('source_host', 160)->nullable()->index();
            $table->string('source_path', 255)->nullable();
            $table->string('landing_path', 255)->nullable();
            $table->string('target_host', 160)->nullable()->index();
            $table->string('target_path', 255)->nullable();
            $table->string('client_token', 64)->nullable()->index();
            $table->string('utm_source', 160)->nullable()->index();
            $table->string('utm_medium', 160)->nullable()->index();
            $table->string('utm_campaign', 160)->nullable()->index();
            $table->string('utm_content', 160)->nullable();
            $table->string('utm_term', 160)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('ip_hash', 64)->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_click_events');
    }
};
