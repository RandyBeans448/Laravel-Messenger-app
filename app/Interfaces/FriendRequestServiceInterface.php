<?php

namespace App\Interfaces;
use Illuminate\Http\JsonResponse;

interface FriendRequestServiceInterface
{
    public function addFriend(string $userId, string $createFriendDto): JsonResponse;

    public function getReceivedFriendRequests(string $userId): JsonResponse; 
}
