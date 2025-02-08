<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Friend extends Model
{
    use SoftDeletes, HasUuids;


    protected $fillable = [
        'user_id',
        'friend_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    public function friends(): BelongsTo
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    public function conversation(): BelongsTo
    {
       return $this->belongsTo(Conversation::class, 'conversation_id');
    }
}