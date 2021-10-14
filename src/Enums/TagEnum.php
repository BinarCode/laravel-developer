<?php

namespace Binarcode\LaravelDeveloper\Enums;

class TagEnum
{
    public const success = 'success';
    public const info = 'info';
    public const danger = 'danger';
    public const warning = 'warning';

    public static function toArray(): array
    {
        return [
            static::success,
            static::info,
            static::danger,
            static::warning,
        ];
    }

    public static function keyValue(): array
    {
        return [
            static::success => static::success,
            static::info => static::info,
            static::danger => static::danger,
            static::warning => static::warning,
        ];
    }
}
