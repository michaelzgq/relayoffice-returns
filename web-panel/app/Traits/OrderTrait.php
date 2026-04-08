<?php

namespace App\Traits;

use App\Enums\Order\OrderStatus;
use App\Models\Order;

trait OrderTrait
{
    private function getOrderList(array $filters)
    {
        [$column, $direction] = getSorting($filters['sorting_type'] ?? 'latest');

        if ($column == 'name') {
            $column = 'user_id';
        }

        return Order::with(['customer', 'counter', 'details', 'account'])
            ->when(isset($filters['type']) && $filters['type'] !== 'all', function ($query) use ($filters) {
                $query->where('order_status', OrderStatus::fromValue($filters['type'])?->value);
            })
            ->when(isset($filters['start_date']) && isset($filters['end_date']), function ($query) use ($filters) {
                $startDate = date('Y-m-d', strtotime($filters['start_date'])) . ' 00:00:00';
                $endDate = date('Y-m-d', strtotime($filters['end_date'])) . ' 23:59:59';
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when(isset($filters['customer_id']) && $filters['customer_id'] !== 'all', function ($query) use ($filters) {
                $query->whereHas('customer', function ($q) use ($filters) {
                    $q->where('id', $filters['customer_id']);
                });
            })
            ->when(!empty($filters['payment_method_id']), function ($query) use ($filters) {
                $paymentMethodIds = is_array($filters['payment_method_id'])
                    ? $filters['payment_method_id']
                    : [$filters['payment_method_id']];

                $query->whereIn('payment_id', $paymentMethodIds);
            })
            ->when(isset($filters['counter_id']) && $filters['counter_id'] !== 'all', function ($query) use ($filters) {
                $query->whereHas('counter', function ($q) use ($filters) {
                    $q->where('id', $filters['counter_id']);
                });
            })
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('mobile', 'like', "%{$search}%");
                        })
                        ->orWhereHas('counter', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('number', 'like', "%{$search}%");
                        });
                });
            })
            ->when(isset($filters['sorting_type']), function ($query) use ($column, $direction) {
                $query->orderBy($column, $direction);
            })
            ->when(!isset($filters['sorting_type']), function ($query) {
                $query->orderBy('created_at', 'desc');
            });
    }
}
