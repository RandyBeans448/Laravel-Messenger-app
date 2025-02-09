<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Services\ConversationService;
use App\Services\MessageService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $conversationService;
    protected $messageService;

    public function __construct(ConversationService $conversationService, MessageService $messageService)
    {
        $this->conversationService = $conversationService;
        $this->messageService = $messageService;
    }

    public function sendMessage(Request $request, $uuid)
    {
        $conversation = $this->conversationService->getConversationById($uuid);

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        $request->merge(['conversation_id' => $conversation->id]);

        $message = $this->messageService->createMessage($request);
    
        broadcast(new MessageSent($message, $conversation->id))->toOthers();

        return response()->json(['message' => 'Message sent!']);
    }
}
