<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Conversation extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'user_id',
        'friend_id',
        'conversation_id', // Ensure this is fillable
    ];

    public function friends(): HasMany
    {
        return $this->hasMany(Friend::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}