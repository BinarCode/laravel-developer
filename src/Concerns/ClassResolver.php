<?php

namespace Binarcode\LaravelDeveloper\Concerns;

use Binarcode\LaravelDeveloper\Models\DeveloperLog;

trait ClassResolver
{
    public static function model(): string
    {
        return config('developer.model') ?? DeveloperLog::class;
    }
}
