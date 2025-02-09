<?php

namespace App\Services;

use App\Models\FriendRequest;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use App\Interfaces\FriendRequestServiceInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Service class for managing friend requests.
 */
class FriendRequestService implements FriendRequestServiceInterface
{
    protected UserService $userService;
    protected FriendService $friendService;

    /**
     * Constructor
     * 
     * @param UserService $userService
     * @param FriendService $friendService
     */
    public function __construct(UserService $userService, FriendService $friendService)
    {
        $this->userService = $userService;
        $this->friendService = $friendService;
    }

    /**
     * Sends a friend request from one user to another.
     * 
     * @param string $userId The ID of the sender.
     * @param string $friendId The ID of the receiver.
     * @return FriendRequest
     * @throws NotFoundHttpException
     */
    public function addFriend(string $userId, string $friendId): FriendRequest
    {
        // Prevent a user from sending a request to themselves
        if ($userId === $friendId) {
            throw new NotFoundHttpException('Cannot add yourself as a friend.');
        }

        // Check if a friend request already exists between the users
        $existingRequest = FriendRequest::where([
            ['sender_id', $userId],
            ['receiver_id', $friendId]
        ])->first();

        if ($existingRequest) {
            throw new NotFoundHttpException('Friend request already exists.');
        }

        // Retrieve sender and receiver user objects
        $user = $this->userService->getUserById($userId);
        $friendCandidate = $this->userService->getUserById($friendId);

        if (!$user || !$friendCandidate) {
            throw new NotFoundHttpException('User or friend candidate not found.');
        }

        // Create and save the new friend request
        $newFriendRequest = new FriendRequest();
        $newFriendRequest->sender_id = $userId;
        $newFriendRequest->receiver_id = $friendId;
        $newFriendRequest->save();

        return $newFriendRequest;
    }

    /**
     * Retrieves all friend requests received by a user.
     * 
     * @param string $userId The ID of the user.
     * @return Collection
     * @throws Exception
     */
    public function getReceivedFriendRequests(string $userId): Collection
    {
        return FriendRequest::with(['requestSentBy', 'receiver'])
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                        ->orWhere('receiver_id', $userId);
            })
            ->get();
    }

    /**
     * Accepts or declines a friend request based on the user's response.
     * 
     * @param array $data Contains friendRequestId and response (true for accept, false for decline).
     * @return string
     * @throws Exception
     */
    public function resolveFriendRequest(array $data): string
    {
        // Find the friend request with sender and receiver details
        $friendRequest = FriendRequest::with(['sender', 'receiver'])
            ->find($data['friendRequestId']);
    
        if (!$friendRequest) {
            throw new Exception('Friend request not found', 404);
        }

        if ($data['response']) {
            // Accept friend request and create friendship record
            DB::transaction(function () use ($friendRequest) {
                $this->friendService->addFriend($friendRequest);
                $this->deleteFriendRequest($friendRequest->id);
            });

            return 'Friend request accepted';
        }

        // Decline the friend request
        $this->deleteFriendRequest($friendRequest->id);
        return 'Friend request declined';
    }

    /**
     * Deletes a friend request by its ID.
     * 
     * @param string $friendRequestId The ID of the friend request.
     * @return void
     */
    private function deleteFriendRequest(string $friendRequestId): void
    {
        // Delete the friend request from the database
        FriendRequest::destroy($friendRequestId);
    }
}