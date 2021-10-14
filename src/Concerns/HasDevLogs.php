<?php

namespace Binarcode\LaravelDeveloper\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasDevLogs
 *
 * @mixin Model
 *
 * @package Binarcode\LaravelDeveloper\Concerns
 */
trait HasDevLogs
{
    public function devLogs(): MorphMany
    {
        return $this->morphMany(
            ClassResolver::model(),
            'target'
        );
    }
}
