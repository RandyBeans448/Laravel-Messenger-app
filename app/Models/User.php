<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;


class User extends Authenticatable
{

    use HasApiTokens, Notifiable, SoftDeletes, HasUuids;

    protected $keyType = 'string'; // Explicitly define it in the model

    public $incrementing = false;

    protected $casts = [
        'id' => 'string',
        'deleted_at' => 'datetime',
    ];

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

    public function createToken($name, array $abilities = ['*'], $expiresAt = null): NewAccessToken
    {
        $token = $this->tokens()->create([
            'id' => Str::uuid(), // Generate UUID
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(64)),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);

        return new NewAccessToken($token, $plainTextToken); // Ensure proper return format
    }

    public function friends()
    {
        return $this->hasMany(Friend::class);
    }

    public function friendRequests()
    {
        return $this->hasMany(FriendRequest::class, 'user_id');
    }
}
