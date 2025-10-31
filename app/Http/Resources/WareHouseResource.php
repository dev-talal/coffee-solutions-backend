<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WareHouseResource extends JsonResource
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
            'name' => $this->name,
            'ar_name' => $this->ar_name,
            'added_by' => $this->added_by,
            'status' => $this->status,
            'region' => new RegionResource($this->region),
            'regions' => RegionResource::collection($this->warehouseRegions),
            'created_by' => new UserResource($this->createdBy),
            'created_at' => $this->created_at,
        ];
    }
}
