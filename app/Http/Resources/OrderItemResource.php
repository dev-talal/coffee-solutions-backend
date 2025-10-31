<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->total,
            'is_box' => $this->is_box,
            'stock_quantity' => $this->stock_quantity,
            'product_name' => $this->product_name,
            'ar_product_name' => $this->ar_product_name,
            'product_price' => $this->product_price,
            'product_image' => $this->product_image,
            'product_pieces_per_box' => $this->product_pieces_per_box,
            'unit' => $this->unit,
            'ar_unit' => $this->ar_unit,
            'uom_unit' => $this->uom_unit,
            'ar_uom_unit' => $this->ar_uom_unit,
            'product' => new CompactProductResource($this->product),
        ];
    }
}
