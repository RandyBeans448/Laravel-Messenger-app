<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\FriendService;
use App\Models\FriendRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FriendController extends Controller
{
    public function __construct(
        private FriendService $friendService
    ) {}

    /**
     * Accept a friend request and create a friendship
     *
     * @param FriendRequest $friendRequest
     * @return JsonResponse
     */
    public function acceptFriendRequest(FriendRequest $friendRequest): JsonResponse
    {
        try {
            $conversation = $this->friendService->addFriend($friendRequest);
            return response()->json($conversation, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to accept friend request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific friend by ID
     *
     * @param string $id
     * @return JsonResponse
     */
    public function getFriend(string $id): JsonResponse
    {
        try {
            $friend = $this->friendService->getFriendById($id);
            return response()->json($friend);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Friend not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get all friends
     *
     * @return JsonResponse
     */
    public function getAllFriends(): JsonResponse
    {
        try {
            $friends = $this->friendService->getAllFriends();
            return response()->json($friends);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve friends',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all friends for a specific user
     *
     * @param string $userId
     * @return JsonResponse
     */
    public function getUserFriends(string $userId): JsonResponse
    {
        try {
            $friends = $this->friendService->getAllOfUsersFriends($userId);
            return response()->json($friends);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve user friends',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}