<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{

    public static $customer = null;
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
            'session_id' => $this->session_id,
            'user_id' => $this->user_id,
            'quantity' => $this->quantity,
            'is_box' => $this->is_box,
            'product' => new CompactProductResource($this->product),
        ];
    }
}
