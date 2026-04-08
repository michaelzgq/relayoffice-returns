<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderedItemListResource extends JsonResource
{
    public function toArray($request): array
    {
        $productDetails = json_decode($this->product_details, true);

        return [
            'name' => $productDetails['name'],
            'quantity' => $this->quantity,
            'unit_type' => $productDetails['unit_type'] ? Unit::find($productDetails['unit_type'])?->unit_type : null,
            'unit_value' => $productDetails['unit_value'],
            'base_price' => (double)$productDetails['selling_price'],
            'base_tax' => (double)$this->tax_amount,
            'base_discount' => (double)$this->discount_on_product,
            'total_price' => (double)($productDetails['selling_price']) * $this->quantity,
            'image' => $productDetails['image'],
            'total_discount' => (double)($this->discount_on_product * $this->quantity),
            'total_tax' => (double)($this->tax_amount * $this->quantity),
            'does_exist' => (bool)Product::find($productDetails['id']),
        ];
    }
}
