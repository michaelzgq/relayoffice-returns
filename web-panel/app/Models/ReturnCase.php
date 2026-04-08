<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
