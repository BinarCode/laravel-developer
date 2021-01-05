<?php

namespace Binarcode\LaravelDeveloper\Tests\Fixtures;

use Laravel\Sanctum\PersonalAccessToken;

class User extends \Illuminate\Foundation\Auth\User
{
    use \Laravel\Sanctum\HasApiTokens;

    public function createToken(string $name, array $abilities = ['*'])
    {
        return tap(new PersonalAccessToken(), function (PersonalAccessToken $token) {
            $token->plainTextToken = 'foo';
        });
    }
}
