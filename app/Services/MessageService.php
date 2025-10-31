<?php

namespace App\Services;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Models\Message;

class MessageService
{
    protected $messageRepository;
    public function __construct(MessageRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function createMessage(array $data)
    {
        return $this->messageRepository->create($data);
    }

    public function updateMessage($id, array $data)
    {
        return $this->messageRepository->update($id, $data);
    }

    public function deleteMessage($id)
    {
        return $this->messageRepository->delete($id);
    }
    public function find($id)
    {
        return $this->messageRepository->find($id);
    }

    public function getMessages($roomId, $page)
    {
        return $this->messageRepository->getMessages($roomId, $page);
    }
}