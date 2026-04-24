<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnExpectedInbound extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_id',
        'brand_id',
        'product_sku',
        'serial_number',
        'tracking_number',
        'return_reason',
        'expected_condition',
        'source',
        'status',
        'matched_return_case_id',
        'imported_by',
        'company_id',
        'imported_at',
        'raw_payload',
    ];

    protected $casts = [
        'imported_at' => 'datetime',
        'raw_payload' => 'array',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function matchedReturnCase()
    {
        return $this->belongsTo(ReturnCase::class, 'matched_return_case_id');
    }

    public function mismatchSummaryForCase(ReturnCase $case): array
    {
        return array_values(array_filter([
            $this->hasSkuMismatch($case) ? 'Expected SKU does not match the inspected SKU.' : null,
            $this->hasSerialMismatch($case) ? 'Expected serial number does not match the inspected serial.' : null,
            $this->hasConditionMismatch($case) ? 'Expected condition does not match the inspected condition.' : null,
        ]));
    }

    public function hasSkuMismatch(ReturnCase $case): bool
    {
        return $this->product_sku !== null
            && strcasecmp($this->product_sku, (string) $case->product_sku) !== 0;
    }

    public function hasSerialMismatch(ReturnCase $case): bool
    {
        return $this->serial_number !== null
            && strcasecmp($this->serial_number, (string) $case->serial_number) !== 0;
    }

    public function hasConditionMismatch(ReturnCase $case): bool
    {
        return $this->expected_condition !== null
            && $this->expected_condition !== $case->condition_code;
    }

    public static function statusLabels(): array
    {
        return [
            'pending' => 'Expected',
            'in_review' => 'In review',
            'received' => 'Received',
            'exception' => 'Exception',
        ];
    }
}
