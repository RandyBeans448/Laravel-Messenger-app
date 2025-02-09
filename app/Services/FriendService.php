<?php

namespace App\Services;

use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Interfaces\FriendServiceInterface;

/**
 * Service class for handling friend-related operations.
 */
class FriendService implements FriendServiceInterface
{
    /**
     * Constructor
     * 
     * @param UserService $userService
     * @param ConversationService $conversationService
     */
    public function __construct(
        private UserService $userService,
        private ConversationService $conversationService
    ) {}

    /**
     * Adds a new friend connection based on an accepted friend request.
     * 
     * @param FriendRequest $acceptedFriendRequest The accepted friend request object.
     * @return Friend
     * @throws \Exception
     */
    public function addFriend(FriendRequest $acceptedFriendRequest): Friend
    {
        try {
            // Retrieve sender and receiver user objects
            $sender = $this->userService->getUserById($acceptedFriendRequest->sender_id);
            $receiver = $this->userService->getUserById($acceptedFriendRequest->receiver_id);

            // Ensure sender and receiver exist
            if (!$sender || !$receiver || empty($sender->id) || empty($receiver->id)) {
                throw new \Exception('Sender and Receiver IDs are required and must not be null.');
            }

            // Check if friendship already exists
            $existingFriendship = Friend::where([
                ['user_id', '=', $sender->id],
                ['friend_id', '=', $receiver->id]
            ])->exists();
            
            if ($existingFriendship) {
                throw new \Exception('Friendship already exists.');
            }

            // Create reciprocal friend records for both users
            $newFriendForSender = Friend::create([
                'user_id' => $sender->id,
                'friend_id' => $receiver->id
            ]);
   
            $newFriendForReceiver = Friend::create([
                'user_id' => $receiver->id,
                'friend_id' => $sender->id
            ]);

            // Create a conversation for the new friends
            $friendsForConversation = [$newFriendForSender, $newFriendForReceiver];
            $this->conversationService->createConversation($friendsForConversation);

            return $newFriendForSender;
    
        } catch (\Exception $error) {
            // Log any error that occurs
            Log::error('Error adding friend', [
                'friend_request_id' => $acceptedFriendRequest->id,
                'error' => $error->getMessage()
            ]);
            throw $error;
        }
    }
    
    /**
     * Retrieves a friend record by its ID.
     * 
     * @param string $id The ID of the friend record.
     * @return Friend
     * @throws \Exception
     */
    public function getFriendById(string $id): Friend
    {
        try {
            // Retrieve friend record with related data
            $friend = Friend::with(['user', 'friend', 'conversations'])->findOrFail($id);
            return $friend;
        } catch (\Exception $error) {
            Log::error($error);
            throw $error;
        }
    }

    /**
     * Retrieves all friend records.
     * 
     * @return Collection
     * @throws \Exception
     */
    public function getAllFriends(): Collection
    {
        try {
            // Retrieve all friend records with related data
            $friends = Friend::with(['user', 'friend', 'conversations'])->get();
            return $friends;
        } catch (\Exception $error) {
            Log::error($error);
            throw $error;
        }
    }

    /**
     * Retrieves all friends of a specific user.
     * 
     * @param string $userId The ID of the user.
     * @return Collection
     * @throws \Exception
     */
    public function getAllOfUsersFriends(string $userId): Collection
    {
        try {
            // Retrieve all friends associated with the user ID
            $friends = Friend::with(['user', 'friend', 'conversations'])
                ->where('user_id', $userId)
                ->get();

            return $friends;
        } catch (\Exception $error) {
            Log::error($error);
            throw $error;
        }
    }
}
