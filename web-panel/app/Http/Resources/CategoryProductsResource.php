<?php

namespace App\Http\Resources;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $categories = json_decode($this->category_ids, true);
        $position1Category = collect($categories)->firstWhere('position', 1);

        return [
            'id' => $this->id,
            'title' => $this->name,
            'product_code' => $this->product_code,
            'unit_type' => $this->unit_type,
            'unit_value' => (int) $this->unit_value,
            'brand' => Brand::find($this->brand),
            'category' => $position1Category ? Category::find($position1Category['id']) : null,
            'category_ids' => $categories,
            'purchase_price' => $this->purchase_price,
            'selling_price' => $this->selling_price,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'quantity' => $this->quantity,
            'image' => $this->image,
            'supplier' => Supplier::find($this->supplier_id),
          ];
    }
}
