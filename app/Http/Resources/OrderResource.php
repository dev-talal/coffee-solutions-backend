<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'total_amount' => $this->total,
            'tax_amount' => $this->tax,
            'sub_total' => $this->sub_total,
            'payment_method' => $this->payment_method,
            'reason' => $this->reason,
            'driver_id' => $this->driver_id,
            'driver' => new CompactDriverResource($this->driver),
            'user' => new UserResource($this->user),
            'warehouse' => new CompactWareHouseResource($this->warehouse),
            'created_at' => $this->created_at->toDateTimeString(),
            'items' => OrderItemResource::collection($this->items),
            'is_linked' => $this->is_linked,
            'short_address' => $this->short_address,
            'building_number' => $this->building_number,
            'secondary_number' => $this->secondary_number,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'country' => $this->country,
            'address_link' => $this->address_link,
            'ar_short_address' => $this->ar_short_address,
            'ar_building_number' => $this->ar_building_number,
            'ar_secondary_number' => $this->ar_secondary_number,
            'ar_postal_code' => $this->ar_postal_code,
            'ar_city' => $this->ar_city,
            'ar_country' => $this->ar_country,
        ];
    }
}
