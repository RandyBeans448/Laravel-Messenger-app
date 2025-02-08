<?php

namespace App\Interfaces;

use App\Models\Friend;
use App\Models\User;
use App\Models\FriendRequest;
use Illuminate\Http\JsonResponse;

interface FriendServiceInterface
{
 
    public function addFriend(FriendRequest $acceptedFriendRequest): JsonResponse;

    public function getFriendById(string $id): JsonResponse;

    public function getAllFriends(): JsonResponse;
    
    public function getAllOfUsersFriends(string $userId): JsonResponse;

    public function updateFriend(Friend $friend): JsonResponse;
    
    public function deleteFriend(string $id): JsonResponse;

    public function alreadyFriends(string $userId, string $friendId,): bool;

    public function _checkToSeeIfFriendShipExists(User $sender, User $receiver): JsonResponse;

    public function _findFriendship(User $sender, User $receiver): JsonResponse;

    public function _createAndGetFriend(User $sender, User $receiver): JsonResponse;

    public function _createNewFriend(User $sender, User $receiver): JsonResponse;
    
}
