<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowReviewRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'work_email',
        'company_name',
        'role_title',
        'volume_note',
        'workflow_note',
        'submitted_from_host',
        'submitted_from_url',
        'status',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];
}
