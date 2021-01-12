<?php

namespace Binarcode\LaravelDeveloper\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface Sanctumable extends Authenticatable
{
    public function createToken(string $name, array $abilities = ['*']);
}
