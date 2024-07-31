<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ControllerHelpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\WorkCategoryResource;
use App\Models\WorkCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkCategoryController extends Controller
{
    use ControllerHelpers;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getWorkCategories(Request $request): JsonResponse
    {
        $count = WorkCategory::count();
        $query = WorkCategory::query()
            ->with('services')
            ->limit(config('config.paginationLimit'))
            ->offset(($request->input('pageIndex', 1) - 1) * config('config.paginationLimit'));

        if (!empty($request->input('name'))) {
            $query->where('name', 'LIKE', '%' . $request->input('name') . '%');
            $count = WorkCategory::where('name', 'LIKE', '%' . $request->input('name') . '%')->count();
        }

        return response()->json([
            'workCategories' => WorkCategoryResource::collection($query->get()),
            'categoryCount'  => $count,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postWorkCategory(Request $request): JsonResponse
    {
        WorkCategory::create($request->only(['name']));

        return response()->json();
    }

    /**
     * @param WorkCategory $workCategory
     * @return JsonResponse
     */
    public function deleteWorkCategory(WorkCategory $workCategory): JsonResponse
    {
        $workCategory->delete();

        return response()->json();
    }

    /**
     * @param Request $request
     * @param WorkCategory $workCategory
     * @return WorkCategoryResource
     */
    public function updateWorkCategory(
        Request      $request,
        WorkCategory $workCategory
    ): WorkCategoryResource
    {
        $workCategory->update($request->only([
            'name',
            'is_active'
        ]));

        return WorkCategoryResource::make($workCategory);
    }
}
