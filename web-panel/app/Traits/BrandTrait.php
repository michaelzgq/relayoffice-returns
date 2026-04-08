<?php

namespace App\Traits;

use App\CPU\Helpers;
use App\Models\Brand;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait BrandTrait
{
    public function queryList(array $filters)
    {
        [$column, $direction] = getSorting($filters['sorting_type'] ?? 'latest');
        return Brand::when($filters['search'], function ($query) use ($filters) {
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
            ->orderBy($column, $direction)
            ->withCount(['products as product_count']);
    }
}
