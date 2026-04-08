<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnCaseEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_case_id',
        'event_type',
        'title',
        'description',
        'meta',
        'created_by',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function returnCase()
    {
        return $this->belongsTo(ReturnCase::class);
    }
}
