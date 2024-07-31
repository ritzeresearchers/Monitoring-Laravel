<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;
use App\Models\WorkCategory;
use App\Models\Business;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WorkCategoryController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(WorkCategory::with('services')->get());
    }

    /**
     * @param WorkCategory $workCategory
     * @return AnonymousResourceCollection
     */
    public function getWorkCategoryServices(WorkCategory $workCategory): AnonymousResourceCollection
    {
        return ServiceResource::collection($workCategory->services()->active()->get());
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getWorkCategoryBusinesses(Request $request)
    {
        $letter = $request->get('letter');
        $keyword = $request->get('keyword');
        $categoryId = $request->get('category_id');
        $pageIndex = $request->input('pageIndex', 1);

        if (!empty($categoryId)) {
            return Business::active()
                ->whereHas('categories', function ($query) use ($categoryId) {
                    $query->where('work_categories.id', $categoryId);
                })
                ->where('name', 'LIKE', "%{$keyword}%")
                ->orWhere('name', 'LIKE', "{$letter}%")
                ->with('categories')
                ->paginate(15)
                ->appends(compact('categoryId'))
                ->getCollection();
        }

        $workCategoriesQuery = WorkCategory::query()
            ->active()
            ->limit(config('config.paginationLimit'))
            ->offset(($pageIndex - 1) * config('config.paginationLimit'));

        $workCategories = $this
            ->getFilteredByNameWorkCategories($workCategoriesQuery, $letter, $keyword)
            ->map(function ($category) use ($keyword) {
                $businesses = Business::active()
                    ->whereHas('categories', function ($query) use ($category) {
                        $query->where('work_categories.id', $category->id);
                    })
                    ->where('name', 'LIKE', "%{$keyword}%")
                    ->with('categories')
                    ->paginate(15)
                    ->appends(['category_id' => $category->id])
                    ->getCollection();

                $category['businesses'] = $businesses;

                return $category;
            });

        return response()->json($workCategories);
    }

    /**
     * @param $workCategoriesQuery
     * @param $letter
     * @param $keyword
     * @return mixed
     */
    private function getFilteredByNameWorkCategories(
        $workCategoriesQuery,
        $letter,
        $keyword
    )
    {
        if (empty($keyword)) {
            return $workCategoriesQuery
                ->where('name', 'LIKE', "{$letter}%")
                ->get();
        } elseif (!empty($letter)) {
            return $workCategoriesQuery
                ->where('name', 'LIKE', "%{$keyword}%")
                ->orWhere('name', 'LIKE', "{$letter}%")
                ->get();
        } else {
            return $workCategoriesQuery
                ->where('name', 'LIKE', "%{$keyword}%")
                ->get();
        }
    }
}
