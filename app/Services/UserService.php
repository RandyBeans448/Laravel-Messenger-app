<?php

namespace App\Services;

use App\Interfaces\UserServiceInterface;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService implements UserServiceInterface
{
    public function getAllUsers(): JsonResponse
    {
        try {
            $users = User::all(); // Directly using Eloquent's built-in method
            return response()->json($users, 200);
        } catch (\Exception $error) {
            Log::error($error);
            return response()->json(['error' => 'Failed to retrieve users'], 500);
        }
    }

    public function getAllUsersWithNoPendingRequests(string $userId): JsonResponse
    {
        try {
            $users = User::where('id', '!=', $userId)
                ->whereNotIn('id', function ($query) use ($userId) {
                    $query->select('receiver_id')
                        ->from('friend_requests')
                        ->where('request_sent_by_id', $userId);
                })
                ->whereNotIn('id', function ($query) use ($userId) {
                    $query->select('request_sent_by_id')
                        ->from('friend_requests')
                        ->where('receiver_id', $userId);
                })
                ->whereNotIn('id', function ($query) use ($userId) {
                    $query->select('friend_id')
                        ->from('friends')
                        ->where('user_id', $userId);
                })
                ->get();

            return response()->json($users, 200);
        } catch (QueryException $error) {
            Log::error($error);
            return response()->json(['error' => 'Failed to retrieve users'], 500);
        }
    }

    public function getUserById(string $id, array $relations = []): JsonResponse
    {
        try {
            $user = User::with($relations)->findOrFail($id);
            return response()->json($user, 200);
        } catch (ModelNotFoundException $error) {
            Log::error("User with ID {$id} not found.");
            return response()->json(['error' => 'User not found'], 404);
        } catch (QueryException $error) {
            Log::error($error);
            return response()->json(['error' => 'Failed to retrieve user'], 500);
        }
    }

    public function getOtherUsers(string $userId): JsonResponse
    {
        try {
            $users = User::where('id', '!=', $userId)->get();
            return response()->json($users, 200);
        } catch 
        (QueryException $error) {
            Log::error($error);
            return response()->json(['error' => 'Failed to retrieve users'], 500);
        }
    }
}
