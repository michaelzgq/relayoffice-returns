<?php

namespace App\Traits;

use App\Models\Admin;
use Carbon\Carbon;

trait EmployeeTrait
{
    public function queryList(array $filters)
    {
        [$column, $direction] = getSorting($filters['sorting_type'] ?? 'latest');

        return Admin::with(['role'])
            ->where('role_id', '!=','1')
            ->when($filters['search'], function ($query) use ($filters) {
            $key = explode(' ', $filters['search']);
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere(function ($w) use ($value) {
                        $w->where('phone', 'like', "%{$value}%")
                            ->orWhere('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%");
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
