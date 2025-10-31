<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryAddressResource extends JsonResource
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
            'user_id' => $this->user_id,
            'is_link' => $this->is_link,
            'is_default' => $this->is_default,
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
