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
        'expected_inbound_id',
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
        'offline_draft_uuid',
        'sync_status',
        'sync_error',
        'draft_payload',
    ];

    protected $casts = [
        'evidence_complete' => 'boolean',
        'received_at' => 'datetime',
        'inspected_at' => 'datetime',
        'refund_decided_at' => 'datetime',
        'draft_payload' => 'array',
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

    public function expectedInbound()
    {
        return $this->belongsTo(ReturnExpectedInbound::class, 'expected_inbound_id');
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

    public function reviewSignals(): array
    {
        $signals = [];
        $ruleProfile = $this->ruleProfile;
        $triggers = $ruleProfile?->auto_hold_triggers ?? [];

        $push = static function (bool $condition, string $message) use (&$signals): void {
            if ($condition) {
                $signals[] = $message;
            }
        };

        $push($this->inspection_status === 'draft', 'Inspection is still a draft.');
        $push(!$this->evidence_complete, 'Evidence checklist is incomplete.');
        $push((bool) ($ruleProfile?->sku_required) && empty($this->product_sku), 'SKU is required but missing.');
        $push((bool) ($ruleProfile?->serial_required) && empty($this->serial_number), 'Serial number is required but missing.');
        $push((bool) ($ruleProfile?->notes_required) && empty($this->notes), 'Inspector notes are required but missing.');

        foreach ($this->expectedInbound?->mismatchSummaryForCase($this) ?? [] as $mismatch) {
            $signals[] = $mismatch;
        }

        $triggerMessages = [
            'draft_capture' => [$this->inspection_status === 'draft', 'Auto-hold trigger: draft capture.'],
            'missing_evidence' => [!$this->evidence_complete, 'Auto-hold trigger: missing evidence.'],
            'missing_sku' => [(bool) ($ruleProfile?->sku_required) && empty($this->product_sku), 'Auto-hold trigger: missing SKU.'],
            'missing_serial' => [(bool) ($ruleProfile?->serial_required) && empty($this->serial_number), 'Auto-hold trigger: missing serial.'],
            'missing_notes' => [(bool) ($ruleProfile?->notes_required) && empty($this->notes), 'Auto-hold trigger: missing notes.'],
            'wrong_item' => [$this->condition_code === 'wrong_item', 'Auto-hold trigger: wrong item.'],
            'empty_box' => [$this->condition_code === 'empty_box', 'Auto-hold trigger: empty box.'],
            'missing_parts' => [$this->condition_code === 'missing_parts', 'Auto-hold trigger: missing parts.'],
            'opened_damaged' => [$this->condition_code === 'opened_damaged', 'Auto-hold trigger: opened damaged.'],
            'expected_sku_mismatch' => [$this->expectedInbound?->hasSkuMismatch($this) ?? false, 'Auto-hold trigger: expected SKU mismatch.'],
            'expected_serial_mismatch' => [$this->expectedInbound?->hasSerialMismatch($this) ?? false, 'Auto-hold trigger: expected serial mismatch.'],
            'expected_condition_mismatch' => [$this->expectedInbound?->hasConditionMismatch($this) ?? false, 'Auto-hold trigger: expected condition mismatch.'],
        ];

        foreach ($triggers as $trigger) {
            if (isset($triggerMessages[$trigger])) {
                $push($triggerMessages[$trigger][0], $triggerMessages[$trigger][1]);
            }
        }

        return array_values(array_unique($signals));
    }
}
