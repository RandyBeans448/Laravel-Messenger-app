<?php

namespace App\Services;

use App\Models\Message;
use App\Interfaces\MessageServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Service class for handling message-related operations.
 */
class MessageService implements MessageServiceInterface
{
    /**
     * Creates a new message in a conversation.
     * 
     * @param Request $request The HTTP request containing message data.
     * @return Message
     * @throws HttpException
     */
    public function createMessage(Request $request): Message
    {
        try {
            // Get the sender's ID from the authenticated user
            $senderId = $request->user()->id;

            // Create a new message instance
            $newMessage = new Message();
            $newMessage->message = $request->input('message'); // Set message content
            $newMessage->conversation_id = $request->input('conversation_id'); // Assign to a conversation
            $newMessage->sender_id = $senderId; // Assign sender

            // Save the new message to the database
            $newMessage->save();

            return $newMessage;
        } catch (QueryException $error) {
            // Log the database query error
            Log::error('Error creating message: ' . $error->getMessage());
            throw new HttpException(500, 'Error creating message');
        }
    }
}
