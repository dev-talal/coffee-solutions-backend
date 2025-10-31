<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrder implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $message;
    public $receiverIds; 

    public function broadcastAs()
    {
        return 'new.order';
    }

    public function __construct($message, $userIds)
    {
        $this->message = $message;

        $this->receiverIds = is_array($userIds) ? $userIds : [$userIds];
    }

    public function broadcastOn()
    {

        return array_map(function ($id) {
            return new Channel('user.' . $id);
        }, $this->receiverIds);
    }
}
