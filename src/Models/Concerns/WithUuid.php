<?php

namespace Binarcode\LaravelDeveloper\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait WithUuid
{
    protected static function bootWithUuid()
    {
        self::creating(function (Model $model) {
            $model->setAttribute('uuid', (string) Str::uuid());
        });
    }

    public static function whereUuid($uuid)
    {
        return static::query()->where('uuid', $uuid)->firstOrFail();
    }
}
