<?php

namespace App\Repositories\Contracts;

interface RoomRepositoryInterface extends BaseRepositoryInterface
{
    public function getUserRooms(int $userId);
    public function getRoomWithMessages(int $roomId);
}
