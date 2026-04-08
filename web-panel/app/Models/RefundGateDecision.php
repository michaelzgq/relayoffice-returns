<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundGateDecision extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_case_id',
        'status',
        'reason',
        'meta',
        'decided_by',
        'decided_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'decided_at' => 'datetime',
    ];

    public function returnCase()
    {
        return $this->belongsTo(ReturnCase::class);
    }
}
