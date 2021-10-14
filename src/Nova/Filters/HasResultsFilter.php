<?php

namespace Binarcode\LaravelDeveloper\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class HasResultsFilter extends BooleanFilter
{
    public function apply(Request $request, $query, $value)
    {
        if ($condition = data_get($value, 'has')) {
            return $query->whereHas('resultDocuments');
        }

        if ($condition = data_get($value, 'no')) {
            return $query->whereDoesntHave('resultDocuments');
        }

        return $query;
    }

    public function options(Request $request)
    {
        return [
            'Has Results' => 'has',
            'No Results' => 'no',
        ];
    }
}
