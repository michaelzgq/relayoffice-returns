<?php

namespace App\Http\Resources;
use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource
{
    public function toArray($request)
    {
        $categories = json_decode($this->category_ids, true);

        $category = collect($categories)->firstWhere('position', 1);
        $subCategory = collect($categories)->firstWhere('position', 2);

        return [
            'id' => $this->id,
            'title' => $this->name,
            'description' => $this->description,
            'product_code' => $this->product_code,
            'unit_type' => $this->unit,
            'unit_value' => (int) $this->unit_value,
            'brand' => $this->productBelongsToBrand,
            'category' => $category ? Category::find($category['id']) : null,
            'sub_category' => $subCategory ? Category::find($subCategory['id']) : null,
            'category_ids' => $categories,
            'purchase_price' => $this->purchase_price,
            'selling_price' => $this->selling_price,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'quantity' => $this->quantity,
            'reorder_level' => $this->reorder_level,
            'status' => $this->status,
            'image' => $this->image,
            'order_count' => $this->order_count,
            'supplier' => $this->supplier,
            'available_time_started_at' => $this->available_time_started_at,
            'available_time_ended_at' => $this->available_time_ended_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'company_id' => $this->company_id,
            'total_orders' => $this?->orderDetails ? $this->orderDetails->count() : 0,
            'total_sold' => $this?->orderDetails ? $this->orderDetails->sum('quantity') : 0,
            'total_sold_amount' => $this?->orderDetails ? $this->orderDetails->sum('price') : 0,
          ];
    }
}
