<?php

namespace App\Traits;
use App\Models\Product;
use Illuminate\Http\Request;

trait ProductTrait
{
    public function queryList($filters) {
        return  Product::with(['supplier', 'productBelongsToBrand', 'unit', 'orderDetails'])
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('product_code', 'like', '%' . $filters['search'] . '%');
                });
            })
            ->when(isset($filters['min_price']) && isset($filters['max_price']) , function ($query) use ($filters) {
                $query->whereBetween('selling_price', [$filters['min_price'], $filters['max_price']]);
            })
            ->when(isset($filters['only_stock_limited']) && $filters['only_stock_limited'] === true, function ($query){
                $query->whereColumn('quantity', '<=', 'reorder_level');
            })
            ->when(isset($filters['stocks']) && is_array($filters['stocks']), function ($query) use ($filters) {
                $stocks = $filters['stocks'];
                $query->where(function ($query1) use ($stocks){
                    if (in_array('out_of_stock', $stocks)) {
                        $query1->orWhere('quantity', '=', 0);
                    }
                    if (in_array('low_stock', $stocks)) {
                        $query1->orWhere(function ($query2) {
                            $query2->where('quantity', '>', 0)
                                ->whereColumn('quantity', '<', 'reorder_level');
                        });
                    }
                    if (in_array('in_stock', $stocks)) {
                        $query1->orWhere('quantity', '>', 0);
                    }
                });
            })
            ->when(isset($filters['category_ids']) && count($filters['category_ids']) > 0, function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    foreach ($filters['category_ids'] as $category_id) {
                        $q->orWhereJsonContains('category_ids', [['id' => (string)$category_id]]);
                    }
                });
            })
            ->when(isset($filters['subcategory_ids']) && count($filters['subcategory_ids']) > 0, function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    foreach ($filters['subcategory_ids'] as $subcategory_id) {
                        $q->orWhereJsonContains('category_ids', [['id' => (string)$subcategory_id]]);
                    }
                });
            })
            ->when(isset($filters['brand_ids']) && count($filters['brand_ids'])>0 , function ($query) use ($filters){
                $query->whereIn('brand', $filters['brand_ids']);
            })
            ->when(isset($filters['supplier_id']) && $filters['supplier_id'] !='all', function ($query) use ($filters) {
                $query->where('supplier_id', $filters['supplier_id']);
            })
            ->when(isset($filters['availability']) && $filters['availability'] !='all', function ($query) use ($filters) {
                $query->where('status', $filters['availability']);
            })
            ->latest();
    }
}
