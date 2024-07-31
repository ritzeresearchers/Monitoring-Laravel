<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Job;
use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Repositories\JobRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    private JobRepository $jobRepository;

    public function __construct(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getJobs(Request $request): JsonResponse
    {
        $params = $request->all();
        $jobsQuery = $this->jobRepository->getJobItemsOverview($params);
        $jobsCount = $this->jobRepository->getFilteredModelCount($params);

        return response()->json([
            'jobs' => JobResource::collection($jobsQuery->get()),
            'jobsCount' => $jobsCount,
        ]);
    }

    /**
     * @param Request $request
     * @param Job $job
     * @return JobResource
     */
    public function updateJob(
        Request $request,
        Job     $job
    ): JobResource
    {
        $job->update($request->only([
            'title',
            'description',
            'is_active',
            'service_id',
            'category_id'
        ]));

        return JobResource::make($job);
    }

    /**
     * @param Job $job
     * @return JsonResponse
     */
    public function deleteJob(Job $job): JsonResponse
    {
        $job->delete();

        return response()->json();
    }
}
