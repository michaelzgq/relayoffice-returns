<?php

namespace App\Traits;

use App\Models\Account;
use Carbon\Carbon;

trait AccountTrait
{
    public function queryList(array $filters)
    {
        [$column, $direction] = getSorting($filters['sorting_type'] ?? 'latest');

        return Account::when($filters['search'], function ($query) use ($filters) {
            $key = explode(' ', $filters['search']);
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('account', 'like', "%{$value}%");
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
