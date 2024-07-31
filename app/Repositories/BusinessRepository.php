<?php

namespace App\Repositories;

use App\Models\Business;
use App\Repositories\Filters\BusinessFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BusinessRepository extends BaseRepository
{
    use Repository;

    protected Model $model;

    /**
     * @param Business $business
     */
    public function __construct(Business $business)
    {
        parent::__construct($business);
        $this->model = $business;
    }

    protected function filterClass(): string
    {
        return BusinessFilter::class;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function findById(int $id)
    {
        return $this->model::where('id', $id)
            ->active()
            ->with([
                'categories',
                'reviews',
                'leadLocations',
                'services',
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->first();
    }

    /**
     * @param int $id
     * @param array $input
     * @return int
     */
    public function update(int $id, array $input): int
    {
        if (isset($input['work_categories'])) {
            $categories = $input['work_categories'];

            if (!is_array($categories))
                $categories = json_decode($input['work_categories']);

            if (isset($categories)) {
                $this->syncCategory($id, $categories);
            }
        }

        if (isset($input['services'])) {
            $services = $input['services'];

            if (!is_array($services))
                $services = json_decode($input['services']);

            if (isset($services)) {
                $this->syncService($id, $services);
            }
        }

        return $this->model::find($id)->update($input);
    }

    /**
     * @param int $businessId
     * @param $workCategories
     * @return void
     */
    protected function syncCategory(int $businessId, $workCategories)
    {
        $business = $this->model::find($businessId);
        $business->categories()->sync($workCategories);
        $business->save();
    }

    /**
     * @param $model
     * @param $input
     * @return mixed
     */
    protected function save($model, $input)
    {
        $model->fill($input);
        $model->save();

        if (isset($input['work_categories']) && is_array($input['work_categories'])) {
            $this->attachCategory($model->id, $input['work_categories']);
        }

        if (isset($input['services']) && is_array($input['services'])) {
            $this->attachService($model->id, $input['services']);
        }

        return $model;
    }

    /**
     * @param int $businessId
     * @param array $workCategories
     * @return void
     */
    protected function attachCategory(int $businessId, array $workCategories): void
    {
        $business = $this->model::find($businessId);
        $business->categories()->attach($workCategories);
        $business->save();
    }

    /**
     * @param int $businessId
     * @param array $services
     * @return void
     */
    protected function attachService(int $businessId, array $services): void
    {
        $business = $this->model::find($businessId);
        $business->services()->attach($services);
        $business->save();
    }

    /**
     * @param int $businessId
     * @param array $services
     * @return void
     */
    protected function syncService(int $businessId, array $services)
    {
        $business = $this->model::find($businessId);
        $business->services()->sync($services);
        $business->save();
    }

    public function getBusinessItemsOverview(array $params): Builder
    {
        $query = $this->model::query();
        $query = $this->applyFilter($query, $params);

        return $this->applyPagination($query, $params['pageIndex']);
    }

    protected function applyCommonQueries(Builder $query): Builder
    {
        return $query;
    }
}
