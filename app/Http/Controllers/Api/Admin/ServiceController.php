<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ControllerHelpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ControllerHelpers;

    /**
     * @param Request $request
     * @return ServiceResource
     */
    public function postService(Request $request): ServiceResource
    {
        $category = Service::create(array_merge(
            $request->only(['name']),
            ['work_category_id' => $request->category_id])
        );

        return ServiceResource::make($category);
    }

    /**
     * @param Request $request
     * @param Service $service
     * @return ServiceResource
     */
    public function updateService(
        Request $request,
        Service $service
    ): ServiceResource
    {
        $service->update($request->only([
            'name',
            'is_active',
        ]));

        return ServiceResource::make($service);
    }

    /**
     * @param Service $service
     * @return JsonResponse
     */
    public function deleteService(Service $service): JsonResponse
    {
        $service->delete();

        return response()->json();
    }
}
