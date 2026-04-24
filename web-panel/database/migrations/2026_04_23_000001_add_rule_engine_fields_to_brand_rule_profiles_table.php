<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brand_rule_profiles', function (Blueprint $table) {
            $table->json('product_rule_scope')->nullable()->after('recommended_dispositions');
            $table->json('auto_hold_triggers')->nullable()->after('product_rule_scope');
            $table->json('escalation_rules')->nullable()->after('auto_hold_triggers');
            $table->text('reviewer_note_template')->nullable()->after('escalation_rules');
            $table->unsignedInteger('rule_version')->default(1)->after('reviewer_note_template');
        });
    }

    public function down(): void
    {
        Schema::table('brand_rule_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'product_rule_scope',
                'auto_hold_triggers',
                'escalation_rules',
                'reviewer_note_template',
                'rule_version',
            ]);
        });
    }
};
