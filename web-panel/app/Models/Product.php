<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $appends = ['image_fullpath'];
    protected $fillable = [
        'id', 'name', 'description', 'product_code', 'unit_type', 'unit_value', 'brand',
        'category_ids', 'purchase_price', 'selling_price', 'discount_type', 'discount',
        'tax', 'quantity', 'reorder_level', 'status', 'image', 'order_count', 'supplier_id',
        'available_time_started_at', 'available_time_ended_at', 'created_at', 'updated_at', 'company_id'
    ];

    public function getImageFullPathAttribute(): string
    {
        $image = $this->image ?? null;
        $path = asset('assets/admin/svg/components/product-default.svg');
        if (!is_null($image) && Storage::disk('public')->exists('product/' . $image)) {
            $path = asset('storage/product/' . $image);
        }
        return $path;
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_type')->select(['id', 'unit_type']);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function getCategoryAttribute()
    {
        $categoryObjects = json_decode($this->category_ids, true) ?? [];

        $primary = collect($categoryObjects)->firstWhere('position', 1);

        if (!$primary) {
            return null;
        }

        return Category::find((int)$primary['id']);
    }

    public function getSubcategoryAttribute()
    {
        $categoryObjects = json_decode($this->category_ids, true) ?? [];

        $primary = collect($categoryObjects)->firstWhere('position', 2);

        if (!$primary) {
            return null;
        }

        return Category::find((int)$primary['id']);
    }


    public function getCategories()
    {
        // Decode the JSON in the category_ids column to extract the IDs
        $categoryIds = collect(json_decode($this->category_ids))->pluck('id');

        // Query the Category model to get the category names
        return Category::whereIn('id', $categoryIds)->get();
    }

    public function brands()
    {
        return $this->belongsTo(Brand::class, 'brand');
    }

    public function scopeActive($query)
    {
        $query->where('status', 1);
        $validCategoryIds = Category::where('status', 1)
            ->mainCategory()
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
        $query->where(function ($query) use ($validCategoryIds) {
            foreach ($validCategoryIds as $validId) {
                $query->orWhereJsonContains('category_ids', ['id' => $validId]);
            }
        });

        return $query;
    }


    public function productBelongsToBrand()
    {
        return $this->belongsTo(Brand::class, 'brand');
    }

    public function setAvailableTimeStartedAtAttribute($value)
    {
        $this->attributes['available_time_started_at'] = $this->parseTimeTo24Hour($value);
    }

    public function setAvailableTimeEndedAtAttribute($value)
    {
        $this->attributes['available_time_ended_at'] = $this->parseTimeTo24Hour($value);
    }

    protected function parseTimeTo24Hour($value)
    {
        if (!$value) {
            return null;
        }

        $formats = ['h : i A', 'h:i A', 'H:i', 'H:i:s', 'h:i:s A'];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, trim($value))->format('H:i:s');
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            return Carbon::parse($value)->format('H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_id', 'id');
    }
    protected static function booted(): void
    {
        static::addGlobalScope('orderByIdDesc', function (Builder $builder) {
            $builder->orderByDesc('id');
        });
    }
}
