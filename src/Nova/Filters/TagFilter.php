<?php

namespace Binarcode\LaravelDeveloper\Nova\Filters;

use App\Domains\Developer\Models\ExceptionLog;
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
        return [
             'Error' => ExceptionLog::TAG_DANGER,
             'Info' => ExceptionLog::TAG_INFO,
        ];
    }
}
