<?php

namespace App\Services;

use App\Interfaces\ConversationServiceInterface;
use App\Models\Conversation;
use Illuminate\Support\Facades\Log;

class ConversationService implements ConversationServiceInterface
{
    public function createConversation(array $friendsForConversation): Conversation
    {
        try {

            $conversation = Conversation::create([]);
    
            foreach ($friendsForConversation as $friend) {
                $friend->update(['conversation_id' => $conversation->id]);
            }
    
            return $conversation;
    
        } catch (\Exception $error) {
            Log::error('Failed to create conversation', [
                'error' => $error->getMessage(),
                'friends' => $friendsForConversation
            ]);
            throw $error;
        }
    }
    

    public function getConversationById(string $id): Conversation
    {
        try {
            $conversation = Conversation::with([
                'friends',
                'friends.user',
                'messages',
                'messages.sender',
            ])->findOrFail($id);

            return $conversation;
        } catch (\Exception $error) {
            Log::error('Failed to retrieve conversation', [
                'id' => $id,
                'error' => $error->getMessage()
            ]);
            throw $error;
        }
    }

    public function getConversationsForUser(string $userId): Conversation
    {
        try {
            $conversations = Conversation::whereHas('friends.user', function ($query) use ($userId) {
                $query->where('id', $userId);
            })
            ->with(['friends', 'friends.user', 'messages'])
            ->get();

            return $conversations;
        } catch (\Exception $error) {
            Log::error('Failed to retrieve conversations for user', [
                'userId' => $userId,
                'error' => $error->getMessage()
            ]);
            throw $error;
        }
    }

    public function saveConversation(Conversation $conversation): Conversation      
    {
        try {
            $conversation->save();
            return $conversation->fresh();
        } catch (\Exception $error) {
            Log::error('Failed to save conversation', [
                'conversationId' => $conversation->id,
                'error' => $error->getMessage()
            ]);
            throw $error;
        }
    }
}