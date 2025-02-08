<?php

namespace App\Interfaces;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;

interface ConversationServiceInterface
{

    public function createConversation(array $friendsForConversation): Conversation;

    public function getConversationById(string $conversationId): Conversation;

    public function getConversationsForUser(string $userId): Conversation;

    public function saveConversation(Conversation $conversation): Conversation;
    
}
