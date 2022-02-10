<?php

namespace Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\DateFilter;

class FromDateFilter extends DateFilter
{
    private string $column;

    public function __construct($column = 'created_at')
    {
        $this->column = $column;
    }

    public function apply(Request $request, $query, $value)
    {
        return $query->whereDate($this->column, '>=', $value);
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
