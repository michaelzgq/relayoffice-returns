<?php

namespace App\Http\Resources;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Http\Resources\Json\JsonResource;

class StockLimitedProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->name,
            'description' => $this->description,
            'product_code' => $this->product_code,
            'unit_type' => Unit::find($this->unit_type),
            'unit_value' => (int) $this->unit_value,
            'brand' => Brand::find($this->brand),
            'category_ids' => json_decode($this->category_ids),
            'purchase_price' => $this->purchase_price,
            'selling_price' => $this->selling_price,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'quantity' => $this->quantity,
            'reorder_level' => $this->reorder_level,
            'image' => $this->image,
            'order_count' => $this->order_count,
            'supplier' => Supplier::find($this->supplier_id),
            'available_time_started_at' => $this->available_time_started_at,
            'available_time_ended_at' => $this->available_time_ended_at,
          ];
    }
}
