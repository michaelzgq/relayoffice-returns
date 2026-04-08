<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'position',
        'image',
        'status',
        'company_id',
        'created_at',
        'updated_at'
    ];

    protected $appends = ['image_fullpath'];
    public function getImageFullPathAttribute(): string
    {
        $image = $this->image ?? null;
        $path = asset('assets/admin/img/160x160/img2.jpg');
        if (!is_null($image) && Storage::disk('public')->exists('category/' . $image)) {
            $path = asset('storage/category/' . $image);
        }
        return $path;
    }


    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }
    public function scopePosition($query)
    {
        return $query->where('position', '=', 0);
    }
    public function products()
    {
        return $this->hasMany(Product::class,'category_id');
    }

    public function scopeMainCategory($query)
    {
        return $query->where('position', '=', 0);
    }

    public function scopeSubCategory($query)
    {
        return $query->where('position', '=', 1);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('orderByIdDesc', function (Builder $builder) {
            $builder->orderByDesc('id');
        });
    }
}
