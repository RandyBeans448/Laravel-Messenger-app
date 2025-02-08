<?php

namespace App\Services;

use App\Models\FriendRequest;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use App\Interfaces\FriendRequestServiceInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FriendRequestService implements FriendRequestServiceInterface
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function addFriend(string $userId, string $friendId): FriendRequest
    {
        try {
            if ($userId === $friendId) {
                throw new NotFoundHttpException('Cannot add yourself as a friend.');
            }

            // Check if friend request already exists
            $errorxistingRequest = FriendRequest::where([
                ['request_sent_by', $userId],
                ['receiver', $friendId]
            ])->first();

            if ($errorxistingRequest) {
                throw new NotFoundHttpException('Friend request already exists.');
            }

            // Retrieve users using the updated getUserById method
            $user = $this->userService->getUserById($userId);
            $friendCandidate = $this->userService->getUserById($friendId);

            if (!$user || !$friendCandidate) {
                throw new NotFoundHttpException('User or friend candidate not found.');
            }

            // Create and save the friend request
            $newFriendRequest = new FriendRequest();
            $newFriendRequest->request_sent_by = $userId;
            $newFriendRequest->receiver = $friendId;
            $newFriendRequest->save();

            return $newFriendRequest;
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            throw $error;
        }
    }

    public function getReceivedFriendRequests(string $userId): Collection
    {
        try {
            return FriendRequest::with(['requestSentBy', 'receiver'])
                ->where(function ($query) use ($userId) {
                    $query->where('request_sent_by_id', $userId)
                            ->orWhere('receiver_id', $userId);
                })
                ->get();
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            throw $error;
        }
    }
}
