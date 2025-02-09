<?php

namespace App\Services;

use App\Models\Message;
use App\Interfaces\MessageServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MessageService implements MessageServiceInterface
{
    public function createMessage(Request $request): Message
    {
        try {
            $senderId = $request->user()->id;

            $newMessage = new Message();
            $newMessage->message = $request->input('message');
            $newMessage->conversation_id = $request->input('conversation_id');
            $newMessage->sender_id = $senderId;

            $newMessage->save();

            return $newMessage;
        } catch (QueryException $error) {
            printf($error);
            Log::error('Error creating message: ' . $error->getMessage());
            throw new HttpException(500, 'Error creating message');
        }
    }
}
