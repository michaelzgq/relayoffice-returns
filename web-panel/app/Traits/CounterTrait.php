<?php

namespace App\Traits;

use App\Models\Counter;
use Carbon\Carbon;

trait CounterTrait
{
    public function queryList(array $filters)
    {
        [$column, $direction] = getSorting($filters['sorting_type'] ?? 'latest');

        return Counter::when($filters['search'], function ($query) use ($filters) {
            $key = explode(' ', $filters['search']);
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere(function ($w) use ($value) {
                        $w->where('name', 'like', "%{$value}%")
                            ->orWhere('number', 'like', "%{$value}%");
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
