<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    abstract protected function filterClass(): string;
    abstract protected function applyCommonQueries(Builder $query): Builder;

    public function getFilteredModelCount(array $params): int
    {
        $query = $this->model::query();
        $query = $this->applyCommonQueries($query);
        $query = $this->applyFilter($query, $params);
        return $query->count();
    }
    public function applyFilter(Builder $query, array $params): Builder
    {
        if (isset($params['filter'])) {
            $filterClass = $this->filterClass();
            $filter = new $filterClass($query);
            $query = $filter->apply($params['filter']);
        }

        return $query;
    }

    public function applyPagination(Builder $query, int $pageIndex): Builder
    {
        return $query->limit(config('config.paginationLimit'))
            ->orderBy('id')
            ->offset(($pageIndex - 1) * config('config.paginationLimit'))
            ;
    }
}
