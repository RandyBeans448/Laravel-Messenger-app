<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\DefaultModelTrait;

class User extends Authenticatable
{

    use HasApiTokens, Notifiable, SoftDeletes, DefaultModelTrait;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function friends()
    {
        return $this->hasMany(Friend::class);
    }

    public function FriendRequests()
    {
        return $this->hasMany(FriendRequest::class, 'friend_requests');
    }
}
