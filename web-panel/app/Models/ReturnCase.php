<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReturnCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_id',
        'brand_id',
        'brand_rule_profile_id',
        'order_id',
        'product_sku',
        'serial_number',
        'condition_code',
        'disposition_code',
        'inspection_status',
        'refund_status',
        'required_photo_count',
        'evidence_photo_count',
        'evidence_complete',
        'sla_hours',
        'notes',
        'received_at',
        'inspected_at',
        'refund_decided_at',
        'assigned_to',
        'created_by',
        'company_id',
    ];

    protected $casts = [
        'evidence_complete' => 'boolean',
        'received_at' => 'datetime',
        'inspected_at' => 'datetime',
        'refund_decided_at' => 'datetime',
    ];

    public static function conditionOptions(): array
    {
        return [
            'unopened',
            'like_new',
            'opened_resaleable',
            'opened_damaged',
            'wrong_item',
            'empty_box',
            'missing_parts',
            'custom',
        ];
    }

    public static function dispositionOptions(): array
    {
        return [
            'restock',
            'hold',
            'return_to_brand',
            'refurb',
            'destroy',
            'quarantine',
        ];
    }

    public static function refundStatusOptions(): array
    {
        return [
            'hold',
            'ready_to_release',
            'needs_review',
            'released',
        ];
    }

    public static function decisionStatusLabels(): array
    {
        return [
            'hold' => 'Ops hold',
            'ready_to_release' => 'Ready for brand review',
            'needs_review' => 'Needs ops review',
            'released' => 'Decision completed',
        ];
    }

    public static function decisionStatusHelp(): array
    {
        return [
            'hold' => 'Keep the case on an internal hold until the evidence or recommendation is ready.',
            'ready_to_release' => 'Evidence is complete and the case is ready to hand off or release.',
            'needs_review' => 'Ops still needs to review the facts before sharing or finalizing the next step.',
            'released' => 'The final decision has already been executed and the case is closed.',
        ];
    }

    public static function shareExpiryOptions(): array
    {
        return [
            1 => '1 day',
            7 => '7 days',
            30 => '30 days',
        ];
    }

    public static function normalizeShareExpiryDays(?int $days): int
    {
        return array_key_exists($days, static::shareExpiryOptions()) ? $days : 7;
    }

    public static function decisionStatusLabel(?string $status): string
    {
        return static::decisionStatusLabels()[$status] ?? Str::headline((string) $status);
    }

    public static function decisionStatusHelpText(?string $status): string
    {
        return static::decisionStatusHelp()[$status] ?? 'Case status is available in the timeline.';
    }

    public static function photoTypeOptions(): array
    {
        return [
            'front',
            'back',
            'packaging',
            'label',
            'damage_closeup',
            'serial_number',
            'missing_parts',
        ];
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function ruleProfile()
    {
        return $this->belongsTo(BrandRuleProfile::class, 'brand_rule_profile_id');
    }

    public function media()
    {
        return $this->hasMany(ReturnCaseMedia::class)->orderBy('sort_order')->orderByDesc('id');
    }

    public function events()
    {
        return $this->hasMany(ReturnCaseEvent::class)->latest();
    }

    public function refundDecision()
    {
        return $this->hasOne(RefundGateDecision::class);
    }

    public function getSlaAgeHoursAttribute(): int
    {
        $from = $this->received_at ?? $this->created_at ?? Carbon::now();

        return $from->diffInHours(Carbon::now());
    }
}
