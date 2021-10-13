<?php

namespace Binarcode\LaravelDeveloper\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\DateFilter;

class EndingAtFilter extends DateFilter
{
    public $name = 'Next Payment';

    public function apply(Request $request, $query, $value)
    {
        return $query->whereDate('ends_at', $value);
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }
}
