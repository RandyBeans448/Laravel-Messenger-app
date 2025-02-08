<?php

namespace App\Services;

use App\Models\Message;
use App\Interfaces\MessageServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MessageService implements MessageServiceInterface
{
    public function createMessage(Request $request): JsonResponse
    {
        try {
            $newMessage = new Message();
            $newMessage->message = $request->input('message');
            $newMessage->conversation_id = $request->input('conversation_id');
            $newMessage->sender_id = $request->input('sender_id');
            $newMessage->save();

            return response()->json($newMessage, 201);
        } catch (QueryException $e) {
            Log::error('Error creating message: ' . $e->getMessage());
            throw new HttpException(500, 'Error creating message');
        }
    }
}
