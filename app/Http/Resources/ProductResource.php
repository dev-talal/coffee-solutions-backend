<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\Tax;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $discount = calculateDiscount($this->price, auth()->user());
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ar_name' => $this->ar_name,
            'ar_description' => $this->ar_description,
            'code' => $this->code,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'product_category_id' => $this->product_category_id,
            'price' => $this->price,
            'status' => $this->status,
            'is_uom_small' => $this->is_uom_small,
            'pieces_per_box' => $this->pieces_per_box,
            'created_at' => $this->created_at->toDateTimeString(),
            'category' => new ProductCategory($this->productCategory),
            'images' => ProductImageResource::collection($this->images),
            'discount_percent' => $discount['discount_percent'],
            'discount_amount' => $discount['discount_amount'],
            'final_price' => $discount['final_price'],
            'promotion_end_date' => $this->promotion_end_date,
            'is_promotion' => $this->is_promotion,
            'old_price' => $this->price,
            'product_unit_id' => $this->product_unit_id,
            'uom_product_unit_id' => $this->uom_product_unit_id,
            'product_unit' => new ProductUnitResource($this->productUnit),
            'uom_product_unit' => new ProductUnitResource($this->uomProductUnit),
            'prmotion_products' => PromotionProductResource::collection($this->promotionProducts),
            'is_liked' => isLiked(auth()->user(), $this->id),
            'taxes' => Tax::where('status', 1)->get(['name', 'rate'])->toArray(),
        ];
    }
}
