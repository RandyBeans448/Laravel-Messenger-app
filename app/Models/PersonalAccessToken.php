<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasUuids;

    public $incrementing = false; // UUIDs are not auto-incrementing
    protected $keyType = 'string'; // Treat UUID as string

    protected $casts = [
        'id' => 'string', // Explicitly cast UUIDs
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}
