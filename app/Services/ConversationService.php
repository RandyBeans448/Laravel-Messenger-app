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
        // Create a new conversation
        $conversation = Conversation::create([]);

        // Associate each friend with the new conversation
        foreach ($friendsForConversation as $friend) {
            $friend->update(['conversation_id' => $conversation->id]);
        }

        return $conversation;
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
        // Retrieve the conversation with related data
        return Conversation::with([
            'friends',
            'friends.user',
            'messages',
            'messages.sender',
        ])->findOrFail($id);
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
        // Fetch conversations where the user is a participant
        return Conversation::whereHas('friends.user', function ($query) use ($userId) {
            $query->where('id', $userId);
        })
        ->with(['friends', 'friends.user', 'messages'])
        ->get();
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
        // Save the conversation
        $conversation->save();
        return $conversation->fresh();
    }
}