<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    use HasFactory;


    protected $appends = ['image_fullpath'];
    protected $fillable = [
        'id', 'name', 'description', 'image', 'status', 'created_at', 'updated_at', 'company_id'
    ];
    public function getImageFullPathAttribute(): string
    {
        $image = !empty($this->image) ? $this->image : null;
        $path = asset('assets/admin/img/160x160/img2.jpg');
        if (!is_null($image) && Storage::disk('public')->exists('brand/' . $image)) {
            $path = asset('storage/brand/' . $image);
        }
        return $path;
    }

    public function products()
    {
        return $this->hasMany(Product::class,'brand');
    }

    public function ruleProfile()
    {
        return $this->hasOne(BrandRuleProfile::class);
    }

}
