<?php

namespace App\Repositories\Filters;

use Illuminate\Database\Eloquent\Builder;

class BaseFilter
{
    protected Builder $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function apply(string $filterString): Builder
    {
        $filters = $this->parseFilters($filterString);

        foreach ($filters as $key => $value) {
            if (method_exists($this, $key) && !empty($value)) {
                $this->$key($value);
            }
        }

        return $this->builder;
    }

    protected function parseFilters(string $filterString): array
    {
        $filters = explode(',', $filterString);
        $parsedFilters = [];

        foreach ($filters as $filter) {
            [$key, $value] = explode('=', $filter);
            $parsedFilters[$key] = $value;
        }

        return $parsedFilters;
    }
}
