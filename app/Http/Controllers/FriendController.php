<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\FriendService;
use App\Models\FriendRequest;
use Illuminate\Http\JsonResponse;

/**
 * Class FriendController
 *
 * This controller handles friend-related actions such as accepting requests,
 * retrieving specific friends, and listing all friends.
 */
class FriendController extends Controller
{
    /**
     * FriendController constructor.
     *
     * @param FriendService $friendService Service for handling friend-related operations.
     */
    public function __construct(
        private FriendService $friendService
    ) {}

    /**
     * Accept a friend request and create a friendship.
     *
     * @param FriendRequest $friendRequest The friend request to be accepted.
     * @return JsonResponse JSON response indicating success or failure.
     */
    public function acceptFriendRequest(FriendRequest $friendRequest): JsonResponse
    {
        try {
            // Attempt to add a new friend through the friend service.
            $conversation = $this->friendService->addFriend($friendRequest);
            
            // Return success response with conversation data.
            return response()->json($conversation, 201);
        } catch (\Exception $e) {
            // Handle exceptions and return an error response.
            return response()->json([
                'message' => 'Failed to accept friend request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific friend by ID.
     *
     * @param string $id The unique identifier of the friend.
     * @return JsonResponse JSON response containing the friend data or an error message.
     */
    public function getFriend(string $id): JsonResponse
    {
        try {
            // Retrieve friend details by ID from the friend service.
            $friend = $this->friendService->getFriendById($id);
            
            // Return the retrieved friend data as JSON.
            return response()->json($friend);
        } catch (\Exception $e) {
            // Handle errors if friend is not found.
            return response()->json([
                'message' => 'Friend not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get all friends.
     *
     * @return JsonResponse JSON response containing a list of all friends.
     */
    public function getAllFriends(): JsonResponse
    {
        try {
            // Retrieve all friends using the friend service.
            $friends = $this->friendService->getAllFriends();
            
            // Return the list of friends as JSON.
            return response()->json($friends);
        } catch (\Exception $e) {
            // Handle errors if retrieval fails.
            return response()->json([
                'message' => 'Failed to retrieve friends',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all friends for a specific user.
     *
     * @param string $userId The unique identifier of the user.
     * @return JsonResponse JSON response containing the user's friends or an error message.
     */
    public function getUserFriends(string $userId): JsonResponse
    {
        try {
            // Retrieve the list of friends for the specified user.
            $friends = $this->friendService->getAllOfUsersFriends($userId);
            
            // Return the user's friends as JSON.
            return response()->json($friends);
        } catch (\Exception $e) {
            // Handle errors if retrieval fails.
            return response()->json([
                'message' => 'Failed to retrieve user friends',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
