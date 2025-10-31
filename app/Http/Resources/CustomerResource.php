<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company_name' => $this->company_name,
            'ar_company_name' => $this->ar_company_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'customer_code' => $this->customer_code,
            'registration_number' => $this->registration_number,
            'vat_number' => $this->vat_number,
            'region_id' => $this->region_id,
            'city_id' => $this->city_id,
            'warehouse_id' => $this->warehouse_id,
            'customer_category_id' => $this->customer_category_id,
            'customer_care_id' => $this->customer_care_id,
            'sales_id' => $this->sales_id,
            'credit_limit' => $this->credit_limit,
            'city' => CompactCityResource::make($this->city),
            'region' => CompactRegionResource::make($this->region),
            'warehouse' => CompactWareHouseResource::make($this->warehouse),
            'created_at' => $this->created_at->toDateTimeString(),
            'delivery_address' => DeliveryAddressResource::make($this->deliveryAddress?->first()),

        ];
    }
}
