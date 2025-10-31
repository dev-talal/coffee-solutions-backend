<?php

namespace App\Services;

use App\Repositories\Contracts\RoomRepositoryInterface;

class RoomService
{
    protected $rooms;

    public function __construct(RoomRepositoryInterface $rooms)
    {
        $this->rooms = $rooms;
    }

    public function find($id)
    {
        return $this->rooms->find($id);
    }

    public function getAllRooms()
    {
        return $this->rooms->all();
    }

    public function getRoom($id)
    {
        return $this->rooms->getRoomWithMessages($id);
    }

    public function createRoom(array $data)
    {
        return $this->rooms->create($data);
    }

    public function updateRoom($id, array $data)
    {
        return $this->rooms->update($id, $data);
    }

    public function deleteRoom($id)
    {
        return $this->rooms->delete($id);
    }

    public function getUserRooms($userId=null)
    {
        $userId = $userId ?? auth()->id();
        return $this->rooms->getUserRooms($userId);
    }

    public function getReceiverId($roomId)
    {
        $room = $this->rooms->find($roomId);
        return $room->customer_care_agent == auth()->user()->id ? $room->user_id : $room->customer_care_agent;
    }
}
