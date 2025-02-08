<?php

namespace App\Interfaces;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;

interface ConversationServiceInterface
{

    public function createConversation(array $friendsForConversation): JsonResponse;

    public function getConversationById(string $conversationId): JsonResponse;

    public function getConversationsForUser(string $userId): JsonResponse;

    public function savedConversation(Conversation $conversation): JsonResponse;
    
}
