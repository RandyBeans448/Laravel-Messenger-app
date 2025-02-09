<?php

namespace App\Http\Controllers;

use App\Services\FriendRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Http\Requests\ResolveFriendRequestRequest;
use Exception;

class FriendRequestController extends Controller 
{
    protected $friendRequestService;

    public function __construct(FriendRequestService $friendRequestService)
    {
        $this->friendRequestService = $friendRequestService;
    }

    /**
     * Send a friend request to another user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendRequest(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'friend_id' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $friendRequest = $this->friendRequestService->addFriend(
                $request->user()->id,
                $request->friend_id
            );

            return response()->json([
                'message' => 'Friend request sent successfully',
                'data' => $friendRequest
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get all received friend requests for the authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getReceivedRequests(Request $request): JsonResponse
    {
        try {
            $requests = $this->friendRequestService->getReceivedFriendRequests(
                $request->user()->id
            );

            return response()->json([
                'data' => $requests
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function resolveFriendRequest(ResolveFriendRequestRequest $request)
    {
        try {
            $message = $this->friendRequestService->resolveFriendRequest($request->validated());
            return response()->json(['message' => $message], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}