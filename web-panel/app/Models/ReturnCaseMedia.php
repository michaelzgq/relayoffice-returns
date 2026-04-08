<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ReturnCaseMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_case_id',
        'file_path',
        'media_type',
        'capture_type',
        'sort_order',
        'uploaded_by',
    ];

    protected $appends = ['file_fullpath'];

    public function returnCase()
    {
        return $this->belongsTo(ReturnCase::class);
    }

    public function getFileFullpathAttribute(): string
    {
        if (!empty($this->file_path) && Storage::disk('public')->exists('return-cases/' . $this->file_path)) {
            return asset('storage/return-cases/' . $this->file_path);
        }

        return asset('assets/admin/svg/components/product-default.svg');
    }
}
