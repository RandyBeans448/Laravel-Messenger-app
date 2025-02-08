<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\DefaultModelTrait;

class Friend extends Model
{
    use SoftDeletes, DefaultModelTrait;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function friend(): HasMany
    {
        return $this->hasMany(Friend::class, 'parent_id', 'id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'friend_id', 'id');
    }
}