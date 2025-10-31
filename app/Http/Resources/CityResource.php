<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            'name'  => $this->name,
            'ar_name'  => $this->ar_name,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'region' => new RegionResource($this->region),
        ];
    }
}
