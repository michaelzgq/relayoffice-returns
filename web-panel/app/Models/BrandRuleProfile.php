<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandRuleProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'profile_name',
        'allowed_conditions',
        'allowed_dispositions',
        'recommended_dispositions',
        'product_rule_scope',
        'auto_hold_triggers',
        'escalation_rules',
        'reviewer_note_template',
        'rule_version',
        'required_photo_types',
        'required_photo_count',
        'notes_required',
        'sku_required',
        'serial_required',
        'default_refund_status',
        'active',
        'company_id',
    ];

    protected $casts = [
        'allowed_conditions' => 'array',
        'allowed_dispositions' => 'array',
        'recommended_dispositions' => 'array',
        'product_rule_scope' => 'array',
        'auto_hold_triggers' => 'array',
        'escalation_rules' => 'array',
        'required_photo_types' => 'array',
        'rule_version' => 'integer',
        'notes_required' => 'boolean',
        'sku_required' => 'boolean',
        'serial_required' => 'boolean',
        'active' => 'boolean',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function returnCases()
    {
        return $this->hasMany(ReturnCase::class);
    }

    public function recommendedDispositionForCondition(?string $condition): ?string
    {
        if (!$condition) {
            return null;
        }

        $mapping = $this->recommended_dispositions ?? [];

        return isset($mapping[$condition]) && $mapping[$condition] !== ''
            ? (string) $mapping[$condition]
            : null;
    }

    public static function autoHoldTriggerOptions(): array
    {
        return [
            'draft_capture' => 'Draft capture not completed',
            'missing_evidence' => 'Required evidence is missing',
            'missing_sku' => 'Required SKU is missing',
            'missing_serial' => 'Required serial is missing',
            'missing_notes' => 'Required notes are missing',
            'wrong_item' => 'Condition is wrong item',
            'empty_box' => 'Condition is empty box',
            'missing_parts' => 'Condition is missing parts',
            'opened_damaged' => 'Condition is opened damaged',
            'expected_sku_mismatch' => 'Expected SKU mismatch',
            'expected_serial_mismatch' => 'Expected serial mismatch',
            'expected_condition_mismatch' => 'Expected condition mismatch',
        ];
    }
}
