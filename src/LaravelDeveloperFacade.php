<?php

namespace Binarcode\LaravelDeveloper;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Binarcode\LaravelDeveloper\LaravelDeveloper
 */
class LaravelDeveloperFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-developer';
    }
}
