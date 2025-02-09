<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get users with no pending friend requests
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUsersWithNoPendingRequests(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $users = $this->userService->getAllUsersWithNoPendingRequests($userId);
            return response()->json([
                'status' => 'success',
                'data' => $users
            ]);
        } catch (\Exception $error) {
            Log::error('Error fetching users with no pending requests: ' . $error->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch users'
            ], 500);
        }
    }

    /**
     * Get user by ID
     *
     * @param string $id
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserById(string $id, Request $request): JsonResponse
    {
        try {
            $relations = $request->input('relations', []);
            $user = $this->userService->getUserById($id, $relations);
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $user
            ]);
        } catch (\Exception $error) {
            Log::error('Error fetching user by ID: ' . $error->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user'
            ], 500);
        }
    }

    /**
     * Get all users except the authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getOtherUsers(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $users = $this->userService->getOtherUsers($userId);
            return response()->json([
                'status' => 'success',
                'data' => $users
            ]);
        } catch (\Exception $error) {
            Log::error('Error fetching other users: ' . $error->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch users'
            ], 500);
        }
    }
}