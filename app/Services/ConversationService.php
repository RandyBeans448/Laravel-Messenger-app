<?php

namespace App\Services;

use App\Interfaces\ConversationServiceInterface;
use App\Models\Conversation;
use Illuminate\Support\Facades\Log;

/**
 * Service class for handling conversations.
 */
class ConversationService implements ConversationServiceInterface
{
    /**
     * Creates a new conversation and associates friends with it.
     * 
     * @param array $friendsForConversation List of friends to be included in the conversation.
     * @return Conversation
     * @throws \Exception
     */
    public function createConversation(array $friendsForConversation): Conversation
    {
        try {
            // Create a new conversation
            $conversation = Conversation::create([]);
    
            // Associate each friend with the new conversation
            foreach ($friendsForConversation as $friend) {
                $friend->update(['conversation_id' => $conversation->id]);
            }
    
            return $conversation;
    
        } catch (\Exception $error) {
            // Log the error with relevant details
            Log::error('Failed to create conversation', [
                'error' => $error->getMessage(),
                'friends' => $friendsForConversation
            ]);
            throw $error;
        }
    }
    
    /**
     * Retrieves a conversation by its ID, including friends, messages, and senders.
     * 
     * @param string $id The ID of the conversation.
     * @return Conversation
     * @throws \Exception
     */
    public function getConversationById(string $id): Conversation
    {
        try {
            // Retrieve the conversation with related data
            $conversation = Conversation::with([
                'friends',
                'friends.user',
                'messages',
                'messages.sender',
            ])->findOrFail($id);

            return $conversation;
        } catch (\Exception $error) {
            // Log the error with the conversation ID
            Log::error('Failed to retrieve conversation', [
                'id' => $id,
                'error' => $error->getMessage()
            ]);
            throw $error;
        }
    }

    /**
     * Retrieves all conversations that a specific user is a part of.
     * 
     * @param string $userId The ID of the user.
     * @return Conversation
     * @throws \Exception
     */
    public function getConversationsForUser(string $userId): Conversation
    {
        try {
            // Fetch conversations where the user is a participant
            $conversations = Conversation::whereHas('friends.user', function ($query) use ($userId) {
                $query->where('id', $userId);
            })
            ->with(['friends', 'friends.user', 'messages'])
            ->get();

            return $conversations;
        } catch (\Exception $error) {
            // Log the error with the user ID
            Log::error('Failed to retrieve conversations for user', [
                'userId' => $userId,
                'error' => $error->getMessage()
            ]);
            throw $error;
        }
    }

    /**
     * Saves updates to a conversation and returns the fresh instance.
     * 
     * @param Conversation $conversation The conversation instance to be saved.
     * @return Conversation
     * @throws \Exception
     */
    public function saveConversation(Conversation $conversation): Conversation      
    {
        try {
            // Save the conversation
            $conversation->save();
            return $conversation->fresh();
        } catch (\Exception $error) {
            // Log the error with the conversation ID
            Log::error('Failed to save conversation', [
                'conversationId' => $conversation->id,
                'error' => $error->getMessage()
            ]);
            throw $error;
        }
    }
}
