<?php

namespace App\Interfaces;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Collection;
use App\Models\FriendRequest;

interface FriendRequestServiceInterface
{
    public function addFriend(string $userId, string $createFriendDto): FriendRequest;

    public function getReceivedFriendRequests(string $userId): Collection;
}
