<?php

namespace Binarcode\LaravelDeveloper\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Trait WithOwner
 * @package App\Models\Concerns
 */
trait WithCreator
{
    protected static function bootWithCreator()
    {
        self::creating(function (Model $model) {
            if (Auth::check()) {
                $model->setAttribute('created_by', $model->getAttribute('created_by') ?? Auth::id());
            }
        });
    }
}
