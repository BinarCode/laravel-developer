<?php

namespace Nova\Fields;

use function americanDate;

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
