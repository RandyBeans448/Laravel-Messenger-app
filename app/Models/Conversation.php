<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Traits\HasUuid;

class Conversation extends Model
{
    use SoftDeletes, HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'id' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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