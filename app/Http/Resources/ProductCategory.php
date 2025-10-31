<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategory extends JsonResource
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
            'icon' => $this->icon,
            'status' => $this->status,
            'parent_id' => $this->parent_id,
            'parent' => new CompactProductCategoryResource($this->parent),
            // 'children' => ProductCategory::collection($this->children),
            'created_by' => new UserResource($this->createdBy),
            'created_at' => $this->created_at,
        ];
    }
}
