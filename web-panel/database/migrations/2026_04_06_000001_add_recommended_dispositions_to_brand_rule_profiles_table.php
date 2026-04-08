<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brand_rule_profiles', function (Blueprint $table) {
            $table->json('recommended_dispositions')->nullable()->after('allowed_dispositions');
        });
    }

    public function down(): void
    {
        Schema::table('brand_rule_profiles', function (Blueprint $table) {
            $table->dropColumn('recommended_dispositions');
        });
    }
};
