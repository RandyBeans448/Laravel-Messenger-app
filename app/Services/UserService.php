<?php

namespace App\Services;

use App\Interfaces\UserServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Collection;

class UserService implements UserServiceInterface
{
    public function getAllUsers(): Collection
    {
        try {
            $users = User::all();
            return $users;
        } catch (\Exception $error) {
            Log::error($error);
            throw $error;
        }
    }

    public function getAllUsersWithNoPendingRequests(string $userId): Collection
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

            return $users;
        } catch (QueryException $error) {
            Log::error($error);
            throw $error;
        }
    }

    public function getUserById(string $id, array $relations = []): ?User
    {
        try {
            return User::with($relations)->findOrFail($id);
        } catch (ModelNotFoundException $error) {
            Log::error("User with ID {$id} not found.");
            return null;
        } catch (QueryException $error) {
            Log::error($error);
            throw $error;
        }
    }

    public function getOtherUsers(string $userId): Collection
    {
        try {
            $users = User::where('id', '!=', $userId)->get();
            return $users;
        } catch (QueryException $error) {
            Log::error($error);
            throw $error;
        }
    }
}
