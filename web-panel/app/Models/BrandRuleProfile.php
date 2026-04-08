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
        'required_photo_types' => 'array',
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
}
