<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Filters\CustomerFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserRepository extends BaseRepository
{
    use Repository;

    protected Model $model;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->model = $user;
    }

    protected function filterClass(): string
    {
        return CustomerFilter::class;
    }

    public function getCustomerItemsOverview(array $params): Builder
    {
        $query = $this->model::query();
        $query = $this->applyCommonQueries($query);
        $query = $this->applyFilter($query, $params);
        return $this->applyPagination($query, $params['pageIndex']);
    }

    public function applyCommonQueries(Builder $query): Builder
    {
        return $query->where('user_type', config('constants.accountType.customer'));
    }
}
