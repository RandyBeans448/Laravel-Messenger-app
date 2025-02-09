<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Services\ConversationService;
use App\Services\MessageService;
use Illuminate\Http\Request;

/**
 * Class ChatController
 *
 * This controller handles chat message sending.
 */
class ChatController extends Controller
{
    /**
     * @var ConversationService $conversationService Service to manage conversations.
     */
    protected $conversationService;

    /**
     * @var MessageService $messageService Service to manage messages.
     */
    protected $messageService;

    /**
     * ChatController constructor.
     *
     * @param ConversationService $conversationService Service for handling conversations.
     * @param MessageService $messageService Service for handling messages.
     */
    public function __construct(ConversationService $conversationService, MessageService $messageService)
    {
        // Initialize conversation service.
        $this->conversationService = $conversationService;
        
        // Initialize message service.
        $this->messageService = $messageService;
    }

    /**
     * Sends a message within a conversation.
     *
     * @param Request $request HTTP request object containing message details.
     * @param string $uuid Unique identifier of the conversation.
     * @return \Illuminate\Http\JsonResponse JSON response with status.
     */
    public function sendMessage(Request $request, $uuid)
    {
        // Retrieve the conversation by its UUID.
        $conversation = $this->conversationService->getConversationById($uuid);

        // If conversation is not found, return a 404 error response.
        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        // Merge conversation ID into request data.
        $request->merge(['conversation_id' => $conversation->id]);

        // Create a new message using the message service.
        $message = $this->messageService->createMessage($request);
    
        // Broadcast the message to other users in the conversation.
        broadcast(new MessageSent($message, $conversation->id))->toOthers();

        // Return success response.
        return response()->json(['message' => 'Message sent!']);
    }
}
