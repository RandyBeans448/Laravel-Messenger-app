<?php

namespace App\Interfaces;

use App\Models\Friend;
use App\Models\FriendRequest;
use Illuminate\Database\Eloquent\Collection;

interface FriendServiceInterface
{
 
    public function addFriend(FriendRequest $acceptedFriendRequest): Friend;

    public function getFriendById(string $id): Friend;

    public function getAllFriends(): Collection;
    
    public function getAllOfUsersFriends(string $userId): Collection;

}
