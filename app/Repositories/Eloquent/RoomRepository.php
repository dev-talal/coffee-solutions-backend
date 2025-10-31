<?php

namespace App\Repositories\Eloquent;

use App\Models\Room;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\RoomRepositoryInterface;

class RoomRepository extends BaseRepository implements RoomRepositoryInterface
{
    public function all()
    {
        return Room::all();
    }

    public function find($id)
    {
        return Room::findOrFail($id);
    }

    public function create(array $data)
    {
        return Room::create($data);
    }

    public function update($id, array $data)
    {
        $room = Room::findOrFail($id);
        $room->update($data);
        return $room;
    }

    public function delete($id)
    {
        $room = Room::findOrFail($id);
        return $room->delete();
    }

    public function getUserRooms(int $userId)
    {
        return Room::where('user_id', $userId)
        ->orWhere('customer_care_agent', $userId)
        ->with(['messages' => function ($query) {
            $query->latest()->limit(1);
        }])
        ->get();
    }

    public function getRoomWithMessages(int $roomId)
    {
        return Room::with('messages')->findOrFail($roomId);
    }
}
