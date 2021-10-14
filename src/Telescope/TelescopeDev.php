<?php

namespace Binarcode\LaravelDeveloper\Telescope;

class TelescopeDev
{
    public static function allow(): bool
    {
        if (! class_exists('Laravel\\Telescope\\Telescope')) {
            logger('Telescope is not installed.');

            return false;
        }

        return config('developer.telescope');
    }
}
