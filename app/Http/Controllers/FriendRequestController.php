<?php

namespace App\Http\Controllers;

use App\Services\FriendRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Http\Requests\ResolveFriendRequestRequest;
use Exception;

/**
 * Class FriendRequestController
 *
 * This controller handles sending, retrieving, and resolving friend requests.
 */
class FriendRequestController extends Controller 
{
    /**
     * @var FriendRequestService $friendRequestService Service to manage friend requests.
     */
    protected $friendRequestService;

    /**
     * FriendRequestController constructor.
     *
     * @param FriendRequestService $friendRequestService Service for handling friend requests.
     */
    public function __construct(FriendRequestService $friendRequestService)
    {
        // Initialize the friend request service.
        $this->friendRequestService = $friendRequestService;
    }

    /**
     * Send a friend request to another user.
     *
     * @param Request $request HTTP request object containing friend request details.
     * @return JsonResponse JSON response indicating success or failure.
     */
    public function sendRequest(Request $request): JsonResponse
    {
        try {
            // Validate the request parameters.
            $validator = Validator::make($request->all(), [
                'friend_id' => 'required|string'
            ]);

            // If validation fails, return an error response.
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create a new friend request using the service.
            $friendRequest = $this->friendRequestService->addFriend(
                $request->user()->id,
                $request->friend_id
            );

            // Return success response with friend request details.
            return response()->json([
                'message' => 'Friend request sent successfully',
                'data' => $friendRequest
            ], 201);
        } catch (\Exception $e) {
            // Handle exceptions and return an error response.
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get all received friend requests for the authenticated user.
     *
     * @param Request $request HTTP request object.
     * @return JsonResponse JSON response containing received friend requests.
     */
    public function getReceivedRequests(Request $request): JsonResponse
    {
        try {
            // Retrieve received friend requests using the service.
            $requests = $this->friendRequestService->getReceivedFriendRequests(
                $request->user()->id
            );

            // Return the list of received friend requests as JSON.
            return response()->json([
                'data' => $requests
            ], 200);
        } catch (\Exception $e) {
            // Handle exceptions and return an error response.
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Resolve a friend request (accept or decline).
     *
     * @param ResolveFriendRequestRequest $request HTTP request containing resolution details.
     * @return JsonResponse JSON response indicating success or failure.
     */
    public function resolveFriendRequest(ResolveFriendRequestRequest $request): JsonResponse
    {
        try {
            // Process friend request resolution.
            $message = $this->friendRequestService->resolveFriendRequest($request->validated());
            
            // Return success response with message.
            return response()->json(['message' => $message], Response::HTTP_OK);
        } catch (Exception $e) {
            // Handle exceptions and return an error response.
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
