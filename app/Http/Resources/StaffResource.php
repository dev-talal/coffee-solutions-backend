<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
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
            'email' => $this->email,
            'role' => $this->getRoleNames()->first(),
            'phone' => $this->phone,
            'location' => $this->location,
            'employe_number' => $this->employe_number,
            'added_by' => new UserResource($this->createdBy),
            'warehouses' => WareHouseResource::collection($this->warehouses),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
