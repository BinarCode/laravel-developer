<?php

namespace Binarcode\LaravelDeveloper\Nova\Fields;

class CreatedAt extends Line
{
    public static function make(...$arguments)
    {
        if (empty($arguments)) {
            $arguments = ['Placed On', 'created_at'];
        }

        $self = new static(...$arguments);

        $self
            ->asDate()
            ->displayUsing(americanDate());

        return $self;
    }
}
