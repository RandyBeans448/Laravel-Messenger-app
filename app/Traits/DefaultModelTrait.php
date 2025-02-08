<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait DefaultModelTrait
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'id' => 'string',
        'deleted_at' => 'datetime',
    ];

    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
