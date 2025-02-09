<?php

namespace App\Services;

use App\Interfaces\UserServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service class for handling user-related operations.
 */
class UserService implements UserServiceInterface
{
    /**
     * Retrieves all users.
     * 
     * @return Collection
     * @throws \Exception
     */
    public function getAllUsers(): Collection
    {
        try {
            // Retrieve all users from the database
            $users = User::all();
            return $users;
        } catch (\Exception $error) {
            // Log any exception that occurs
            Log::error($error);
            throw $error;
        }
    }

    /**
     * Retrieves all users who have no pending friend requests with the given user.
     * 
     * @param string $userId The ID of the user.
     * @return Collection
     * @throws QueryException
     */
    public function getAllUsersWithNoPendingRequests(string $userId): Collection
    {
        try {
            // Retrieve users who are not already friends or have pending friend requests
            $users = User::where('id', '!=', $userId)
                ->whereNotIn('id', function ($query) use ($userId) {
                    $query->select('receiver_id')
                        ->from('friend_requests')
                        ->where('sender_id', $userId);
                })
                ->whereNotIn('id', function ($query) use ($userId) {
                    $query->select('sender_id')
                        ->from('friend_requests')
                        ->where('receiver_id', $userId);
                })
                ->whereNotIn('id', function ($query) use ($userId) {
                    $query->select('friend_id')
                        ->from('friends')
                        ->where('user_id', $userId);
                })
                ->get();

            return $users;
        } catch (QueryException $error) {
            // Log any database query error
            Log::error($error);
            throw $error;
        }
    }

    /**
     * Retrieves a user by ID with optional relationships.
     * 
     * @param string $id The ID of the user.
     * @param array $relations The relationships to load.
     * @return User|null
     */
    public function getUserById(string $id, array $relations = []): ?User
    {
        try {
            // Retrieve user with specified relationships
            return User::with($relations)->findOrFail($id);
        } catch (ModelNotFoundException $error) {
            // Log the error if user is not found
            Log::error("User with ID {$id} not found.");
            return null;
        } catch (QueryException $error) {
            // Log any database query error
            Log::error($error);
            throw $error;
        }
    }

    /**
     * Retrieves all users except the given user.
     * 
     * @param string $userId The ID of the user to exclude.
     * @return Collection
     * @throws QueryException
     */
    public function getOtherUsers(string $userId): Collection
    {
        try {
            // Retrieve all users excluding the given user ID
            $users = User::where('id', '!=', $userId)->get();
            return $users;
        } catch (QueryException $error) {
            // Log any database query error
            Log::error($error);
            throw $error;
        }
    }
}
