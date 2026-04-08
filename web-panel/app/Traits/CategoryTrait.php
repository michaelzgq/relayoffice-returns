<?php

namespace App\Traits;

use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait CategoryTrait
{
    public function countProducts(array $categoryIds)
    {
        return Product::selectRaw('category_ids')
            ->where(function ($query) use ($categoryIds) {
                foreach ($categoryIds as $id) {
                    $query->orWhereJsonContains('category_ids', ['id' => (string)$id]);
                }
            })
            ->get()
            ->flatMap(function ($product) use ($categoryIds) {
                return collect(json_decode($product->category_ids, true))
                    ->pluck('id')
                    ->filter(fn($id) => in_array($id, $categoryIds));
            })
            ->countBy()
            ->toArray();
    }

    public function queryList(array $filters)
    {
        [$column, $direction] = getSorting($filters['sorting_type'] ?? 'latest');

        return Category::when(isset($filters['category_id']), function ($query) use ($filters) {
            $query->where('parent_id', $filters['category_id']);
        })
            ->when($filters['search'], function ($query) use ($filters) {
                $key = explode(' ', $filters['search']);
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($filters['start_date']) && isset($filters['end_date']), function ($query) use ($filters) {
                $start = Carbon::parse($filters['start_date'])->startOfDay();
                $end = Carbon::parse($filters['end_date'])->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            })
            ->orderBy($column, $direction);
    }
}
