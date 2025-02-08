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
            $conversation = DB::transaction(function () use ($acceptedFriendRequest) {
                // Get both users
                $senderResponse = $this->userService->getUserById($acceptedFriendRequest->request_sent_by_id);
                $receiverResponse = $this->userService->getUserById($acceptedFriendRequest->receiver_id);

                if ($senderResponse->getStatusCode() !== 200 || $receiverResponse->getStatusCode() !== 200) {
                    throw new \Exception('Failed to retrieve users');
                }

                $sender = json_decode($senderResponse->getContent());
                $receiver = json_decode($receiverResponse->getContent());

                // Create friend records for both users
                $newFriendForSender = Friend::create([
                    'user_id' => $sender->id,
                    'friend_user_id' => $receiver->id
                ]);

                $newFriendForReceiver = Friend::create([
                    'user_id' => $receiver->id,
                    'friend_user_id' => $sender->id
                ]);

                // Create and save the conversation
                $conversationResponse = $this->conversationService->createConversation([
                    $newFriendForSender->id,
                    $newFriendForReceiver->id
                ]);

                if ($conversationResponse->getStatusCode() !== 201) {
                    throw new \Exception('Failed to create conversation');
                }

                $conversation = json_decode($conversationResponse->getContent());

                // Update friend records with conversation
                $newFriendForSender->update(['conversation_id' => $conversation->id]);
                $newFriendForReceiver->update(['conversation_id' => $conversation->id]);

                // Get the complete conversation with relationships
                return Conversation::with([
                    'friends',
                    'friends.user',
                    'messages',
                    'messages.sender'
                ])->findOrFail($conversation->id);
            });

            return $conversation;

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