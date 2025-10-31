<?php

namespace App\Repositories\Eloquent;

use App\Models\Message;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class MessageRepository extends BaseRepository implements MessageRepositoryInterface
{
    public function __construct(Message $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return Message::create($data);
    }

    public function update($id, array $data)
    {
        $message = Message::findOrFail($id);
        $message->update($data);
        return $message;
    }

    public function getMessages($roomId, $page)
    {
        $userId = auth()->id();

        $query = Message::where('room_id', $roomId);

        $unreadCount = (clone $query)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', 0)
            ->count();

        if ((int) $page === 1 && $unreadCount > 0) {
            (clone $query)
                ->where('sender_id', '!=', $userId)
                ->where('is_read', 0)
                ->update(['is_read' => 1]);

            $unreadCount = 0;
        }

        $messages = $query
            ->orderByDesc('created_at')
            ->paginate(20, ['*'], 'page', $page);

        return [
            'data' => $messages,
            'unread_count' => $unreadCount,
        ];
    }


    public function delete($id)
    {
        $message = Message::findOrFail($id);
        return $message->delete();
    }
}