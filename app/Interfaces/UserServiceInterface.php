<?php

namespace App\Interfaces;
use Illuminate\Http\JsonResponse;

interface UserServiceInterface
{
    public function getAllUsers(): JsonResponse;

    public function getAllUsersWithNoPendingRequests(string $userId): JsonResponse;

    public function getUserById(string $userId, array $relations): JsonResponse;

    public function getOtherUsers(string $userId): JsonResponse;
}
