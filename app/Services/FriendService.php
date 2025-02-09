<?php

namespace App\Services;

use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Interfaces\FriendServiceInterface;


class FriendService implements FriendServiceInterface
{
    public function __construct(
        private UserService $userService,
        private ConversationService $conversationService
    ) {}

    public function addFriend(FriendRequest $acceptedFriendRequest): Friend
    {

        try {

            $sender = $this->userService->getUserById($acceptedFriendRequest->sender_id);
            $receiver = $this->userService->getUserById($acceptedFriendRequest->receiver_id);

            if (!$sender || !$receiver || empty($sender->id) || empty($receiver->id)) {
                throw new \Exception('Sender and Receiver IDs are required and must not be null.');
            }

            $existingFriendship = Friend::where([
                ['user_id', '=', $sender->id],
                ['friend_id', '=', $receiver->id]
            ])->exists();
            
            if ($existingFriendship) {
                throw new \Exception('Friendship already exists.');
            }

            $newFriendForSender = Friend::create([
                'user_id' => $sender->id,
                'friend_id' => $receiver->id
            ]);
   
            $newFriendForReceiver = Friend::create([
                'user_id' => $receiver->id,
                'friend_id' => $sender->id
            ]);

            $friendsForConversation = [$newFriendForSender, $newFriendForReceiver];

            $this->conversationService->createConversation($friendsForConversation);
            return $newFriendForSender;
    
        } catch (\Exception $error) {
            Log::error('Error adding friend', [
                'friend_request_id' => $acceptedFriendRequest->id,
                'error' => $error->getMessage()
            ]);
            throw $error;
        }
    }
    

    public function getFriendById(string $id): Friend
    {
        try {
            $friend = Friend::with(['user', 'friend', 'conversations'])->findOrFail($id);
    
            return $friend;
        } catch (\Exception $error) {
            Log::error($error);
            throw $error;
        }
    }

    public function getAllFriends(): Collection
    {
        try {
            $friends = Friend::with(['user', 'friend', 'conversations'])->get();

            return $friends;
        } catch (\Exception $error) {
            Log::error($error);
            throw $error;
        }
    }

    public function getAllOfUsersFriends(string $userId): Collection
    {
        try {
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