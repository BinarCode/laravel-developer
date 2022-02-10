<?php

namespace Nova\Filters;

use Binarcode\LaravelDeveloper\Enums\TagEnum;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class TagFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('tags', $value);
    }

    public function options(Request $request)
    {
        return TagEnum::keyValue();
    }
}
