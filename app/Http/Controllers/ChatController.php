<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Services\ConversationService;
use App\Services\MessageService;
use Illuminate\Http\Request;
use App\Http\Requests\CreateMessageRequest;


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
    public function __construct(
        ConversationService $conversationService,
        MessageService $messageService,
        )
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
    public function sendMessage(CreateMessageRequest $request, $uuid)
    {
        // Retrieve conversation by UUID
        $conversation = $this->conversationService->getConversationById($uuid);
    
        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }
    
        // Get validated data (only 'message' at this point)
        $validatedData = $request->validated();
    
        // Add additional fields not provided by the client
        $validatedData['conversation_id'] = $conversation->id;
        $validatedData['sender_id'] = $request->user()->id;
    
        // Create message with the full dataset
        $message = $this->messageService->createMessage($validatedData);
    
        broadcast(new MessageSent($message, $conversation->id))->toOthers();
    
        return response()->json(['message' => 'Message sent!']);
    }
}
