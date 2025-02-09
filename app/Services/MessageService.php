<?php

namespace App\Services;

use App\Models\Message;
use App\Interfaces\MessageServiceInterface;
use App\Http\Requests\CreateMessageRequest;

class MessageService implements MessageServiceInterface
{
    public function createMessage(CreateMessageRequest $request): Message
    {
        return Message::create([
            'message' => $request->message,
            'conversation_id' => $request->conversation_id,
            'sender_id' => $request->user()->id,
        ]);
    }
}