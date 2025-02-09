<?php

namespace App\Services;

use App\Models\Message;
use App\Interfaces\MessageServiceInterface;

class MessageService implements MessageServiceInterface
{
    public function createMessage(array $data): Message
    {
        return Message::create($data);
    }
}