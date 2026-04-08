<?php

namespace App\Traits;

use App\Models\Coupon;
use Carbon\Carbon;

trait CouponTrait
{
    public function queryList(array $filters)
    {
        [$column, $direction] = getSorting($filters['sorting_type'] ?? 'latest');

        return Coupon::when($filters['search'], function ($query) use ($filters) {
                $key = explode(' ', $filters['search']);
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere(function ($w) use ($value) {
                            $w->where('title', 'like', "%{$value}%")
                              ->orWhere('code', 'like', "%{$value}%");
                        });
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
