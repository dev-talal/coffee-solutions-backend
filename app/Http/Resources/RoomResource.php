<?php
    
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $roomName  = $this->user->first_name . ' - ' .$this->customerCareAgent->first_name;
        return [
            'id' => $this->id,
            'user' => $this->user,
            'customer_care_agent' => $this->customerCareAgent,
            'last_message' => $this->lastMessage,
            'created_at' => $this->created_at->toDateTimeString(),
            'room_name' =>  $roomName,
            'unread_messages_count' => $request->isMethod('GET') ? $this->getUnreadMessagesCount() : $this->getUnreadMessagesCountForCustomer(),
        ];
    }
}