<?php

namespace App\Interfaces;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserServiceInterface
{
    public function getAllUsers(): Collection;

    public function getAllUsersWithNoPendingRequests(string $userId): Collection;

    public function getUserById(string $userId, array $relations): ?User;

    public function getOtherUsers(string $userId): Collection;
}
