<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request, $isSenderCount = false): array
    {
        return [
            'id' => $this->id,
            'room_id' => $this->room_id,
            'sender_id' => $this->sender_id,
            'message' => $this->message,
            'media' => $this->media,
            'room' => new RoomResource($this->room),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}