<?php

namespace App\Repositories;

use App\Models\Job;
use App\Repositories\Filters\JobFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JobRepository extends BaseRepository
{
    use Repository;

    protected Model $model;

    /**
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        parent::__construct($job);
        $this->model = $job;
    }

    protected function filterClass(): string
    {
        return JobFilter::class;
    }

    /**
     * @param int $posterId
     * @return mixed
     */
    public function findActiveByPosterId(int $posterId)
    {
        return $this->model::where('poster_id', $posterId)
            ->where('status', config('constants.jobStatus.active'))
            ->active()
            ->with('service')
            ->orderBy('id', 'desc');
    }

    /**
     * @param int $posterId
     * @return mixed
     */
    public function findCustomerInPgrogressJobByPosterId(int $posterId)
    {
        return $this->model::with('hiredBusiness')
            ->active()
            ->orderBy('id', 'desc')
            ->where('poster_id', $posterId)
            ->where('status', config('constants.jobStatus.inProgress'));
    }

    /**
     * @param int $businessId
     * @return mixed
     */
    public function findBusinessInProgressJobsByBusinessId(int $businessId)
    {
        return $this->model::where('hired_business_id', $businessId)
            ->active()
            ->orderBy('id', 'desc')
            ->where('status', config('constants.jobStatus.inProgress'));
    }

    /**
     * @param int $posterId
     * @return mixed
     */
    public function findCustomerFinishedJobByPosterId(int $posterId)
    {
        return $this->model::with('hiredBusiness')
            ->active()
            ->where('poster_id', $posterId)
            ->finished();
    }

    /**
     * @param int $businessId
     * @return mixed
     */
    public function findBusinessFinishedJobsByBusinessId(int $businessId)
    {
        return $this->model::where('hired_business_id', $businessId)
            ->active()
            ->finished();
    }

    public function getJobItemsOverview(array $params): Builder
    {
        $query = $this->model::query();
        $query = $this->applyCommonQueries($query);
        $query = $this->applyFilter($query, $params);

        return $this->applyPagination($query, $params['pageIndex']);
    }

    public function applyCommonQueries(Builder $query): Builder
    {
        return $query->with('service')->withCount('quotes');
    }
}
