<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FriendRequest extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = ['sender_id', 'receiver_id',];


    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }    

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}