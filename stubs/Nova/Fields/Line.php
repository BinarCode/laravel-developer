<?php

namespace Nova\Fields;

class Line extends \Laravel\Nova\Fields\Line
{
    public const MEDIUM = 'medium';

    public static $classes = [
        self::HEADING => 'text-base font-semibold',
        self::BASE => 'text-base',
        self::SUBTITLE => 'text-xs tracking-loose font-bold uppercase text-80',
        self::SMALL => 'text-xs',
//        self::MEDIUM => 'text-sm',
    ];

    /**
     * Display the line with small styles.
     *
     * @return \Laravel\Nova\Fields\Line
     */
    public function asMedium()
    {
        $this->type = static::MEDIUM;

        return $this;
    }

    public function asDate()
    {
        $this->asMedium();

        return $this;
    }

    public function multiline()
    {
        $this->extraClasses('important-whitespace-normal w-1/2 flex leading-tight');

        return $this;
    }
}
