<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\DefaultModelTrait;

class FriendRequest extends Model
{
    use SoftDeletes, DefaultModelTrait;

    public function requestSentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'request_sent_by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}