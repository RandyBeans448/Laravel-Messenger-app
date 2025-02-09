<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Class UserController
 *
 * This controller handles user-related actions such as retrieving users,
 * getting specific user details, and filtering users with no pending friend requests.
 */
class UserController extends Controller
{
    /**
     * @var UserService $userService Service to manage user operations.
     */
    protected UserService $userService;

    /**
     * UserController constructor.
     *
     * @param UserService $userService Service for handling user operations.
     */
    public function __construct(UserService $userService)
    {
        // Initialize user service.
        $this->userService = $userService;
    }

    /**
     * Get users with no pending friend requests.
     *
     * @param Request $request HTTP request object.
     * @return JsonResponse JSON response containing filtered users.
     */
    public function getUsersWithNoPendingRequests(Request $request): JsonResponse
    {
        try {
            // Retrieve the authenticated user's ID.
            $userId = $request->user()->id;
            
            // Fetch users without pending friend requests.
            $users = $this->userService->getAllUsersWithNoPendingRequests($userId);
            
            // Return the users as JSON response.
            return response()->json([
                'status' => 'success',
                'data' => $users
            ]);
        } catch (\Exception $error) {
            // Log the error and return an error response.
            Log::error('Error fetching users with no pending requests: ' . $error->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch users'
            ], 500);
        }
    }

    /**
     * Get user by ID.
     *
     * @param string $id The unique identifier of the user.
     * @param Request $request HTTP request object.
     * @return JsonResponse JSON response containing the user details or an error message.
     */
    public function getUserById(string $id, Request $request): JsonResponse
    {
        try {
            // Retrieve requested relations from the request input.
            $relations = $request->input('relations', []);
            
            // Fetch the user by ID with the specified relations.
            $user = $this->userService->getUserById($id, $relations);
            
            // If user is not found, return a 404 response.
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            // Return the user details as JSON response.
            return response()->json([
                'status' => 'success',
                'data' => $user
            ]);
        } catch (\Exception $error) {
            // Log the error and return an error response.
            Log::error('Error fetching user by ID: ' . $error->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user'
            ], 500);
        }
    }

    /**
     * Get all users except the authenticated user.
     *
     * @param Request $request HTTP request object.
     * @return JsonResponse JSON response containing a list of other users.
     */
    public function getOtherUsers(Request $request): JsonResponse
    {
        try {
            // Retrieve the authenticated user's ID.
            $userId = $request->user()->id;
            
            // Fetch all users excluding the authenticated user.
            $users = $this->userService->getOtherUsers($userId);
            
            // Return the filtered list of users as JSON response.
            return response()->json([
                'status' => 'success',
                'data' => $users
            ]);
        } catch (\Exception $error) {
            // Log the error and return an error response.
            Log::error('Error fetching other users: ' . $error->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch users'
            ], 500);
        }
    }
}
