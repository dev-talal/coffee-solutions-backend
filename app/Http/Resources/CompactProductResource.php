<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\Tax;
use Illuminate\Http\Resources\Json\JsonResource;

class CompactProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $customer = CartResource::$customer ?? auth()->user();
        $discount = calculateDiscount($this->price, $customer);
        $product = [
            'id' => $this->id,
            'name' => $this->name,
            'ar_name' => $this->ar_name,
            'ar_description' => $this->ar_description,
            'images' => ProductImageResource::collection($this->images),
            'discount_percent' => $discount['discount_percent'],
            'discount_amount' => $discount['discount_amount'],
            'final_price' => $discount['final_price'],
            'old_price' => $this->price,
            'quantity' => $this->quantity,
            'is_promotion' => $this->is_promotion,
            'description' => $this->description,
            'is_uom_small' => $this->is_uom_small,
            'pieces_per_box' => $this->pieces_per_box,
            'is_liked' => isLiked(auth()->user(), $this->id),
            'product_unit_id' => $this->product_unit_id,
            'uom_product_unit_id' => $this->uom_product_unit_id,
            'product_unit' => new ProductUnitResource($this->productUnit),
            'uom_product_unit' => new ProductUnitResource($this->uomProductUnit),
            'taxes' => Tax::where('status', 1)->get(['name', 'rate'])->toArray(),
        ];

        if($this->is_promotion){
            $product['promotion_products'] = PromotionProductResource::collection($this->promotionProducts);
        }

        return $product;
    }
}
