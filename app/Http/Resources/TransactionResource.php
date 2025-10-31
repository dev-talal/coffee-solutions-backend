<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'order_id' => $this->order_id,
            'type' => $this->type,
            'method' => $this->method,
            'amount' => $this->amount,
            'status' => $this->status,
            'customer' => new UserResource($this->customer),
            'created_at' => $this->created_at,
        ];
    }
}
