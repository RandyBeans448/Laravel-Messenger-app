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

class FriendRequestService implements FriendRequestServiceInterface
{
    protected UserService $userService;
    protected FriendService $friendService;

    public function __construct(UserService $userService, FriendService $friendService)
    {
        $this->userService = $userService;
        $this->friendService = $friendService;
    }

    public function addFriend(string $userId, string $friendId): FriendRequest
    {
        try {
            if ($userId === $friendId) {
                throw new NotFoundHttpException('Cannot add yourself as a friend.');
            }

            // Check if friend request already exists
            $errorxistingRequest = FriendRequest::where([
                ['sender_id', $userId],
                ['receiver_id', $friendId]
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
            $newFriendRequest->sender_id = $userId;
            $newFriendRequest->receiver_id = $friendId;
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
                    $query->where('sender_id', $userId)
                            ->orWhere('receiver_id', $userId);
                })
                ->get();
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            throw $error;
        }
    }



    public function resolveFriendRequest(array $data): string
    {
        try {
            $friendRequest = FriendRequest::with(['sender', 'receiver'])
                ->find($data['friendRequestId']);
        

            if (!$friendRequest) {
                throw new Exception('Friend request not found', 404);
            }

            if ($data['response']) {
                DB::transaction(function () use ($friendRequest) {
                    $this->friendService->addFriend($friendRequest);
                    $this->deleteFriendRequest($friendRequest->id);
                });

                return 'Friend request accepted';
            } else {
                $this->deleteFriendRequest($friendRequest->id);
                return 'Friend request declined';
            }
        } catch (Exception $e) {
            throw new Exception('Error resolving this friendship request: ' . $e->getMessage(), $e->getCode());
        }
    }

    private function deleteFriendRequest(string $friendRequestId): void
    {
        FriendRequest::destroy($friendRequestId);
    }
}
