<?php

namespace App\Http\Controllers\Api;

use App\Events\JobPosted;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Models\Location;
use App\Repositories\JobRepository;
use App\Repositories\QuoteRepository;
use App\Services\Strategy\Jobs\BusinessCancellationStrategy;
use App\Services\Strategy\Jobs\CustomerCancellationStrategy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JobController extends Controller
{
    use ControllerHelpers;

    private JobRepository $jobRepository;
    private QuoteRepository $quoteRepository;

    /**
     * @param JobRepository $jobRepository
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        JobRepository   $jobRepository,
        QuoteRepository $quoteRepository
    )
    {
        $this->jobRepository = $jobRepository;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param SaveJobRequest $request
     * @return JobResource|JsonResponse
     */
    public function postJob(SaveJobRequest $request)
    {
        if ($error = $this->getJobPropertiesValidationError($request)) {
            return $this->respondError($error);
        }

        /* @var $job Job */
        $job = $this->user()->jobs()->create(array_merge($request->only([
            'title',
            'description',
            'service_id',
            'category_id',
            'target_job_done',
            'other_details',
            'rate_type',
            'job_type',
            'pages',
            'amount',
        ]), [
            'location_id'                => Location::firstWhere('location', $request->get('location'))->id,
            'target_completion_datetime' => $this->getTargetCompletionDatetime($request),
            'status'                     => config('constants.jobStatus.active'),
        ]));
// Todo Enable this for email verification
        // event(new JobPosted([
        //     'first_name' => $this->user()->first_name,
        //     'last_name'  => $this->user()->last_name,
        // ], $job));
       
        return JobResource::make($job);
        
    }

    /**
     * @param Job $job
     * @param Request $request
     * @return JobResource|JsonResponse
     * @throws ValidationException
     */
    public function updateJobStatus(
        Request $request,
        Job     $job
    )
    {
        $this->validate($request, ['status' => 'required']);

        $jobStatuses = array_keys(config('constants.jobStatus'));
        $newJobStatus = $request->get('status');

        if (!in_array($newJobStatus, $jobStatuses, true)) {
            return $this->respondError('Invalid job status.');
        }

        if ($newJobStatus === 'cancelled' && $job->hiredBusiness) {

            $strategy = $this->user()->isCustomer()
                ? new CustomerCancellationStrategy()
                : new BusinessCancellationStrategy();

            $strategy->cancel($job);
        }

        $job->update(['status' => $newJobStatus]);

        return JobResource::make($job);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getJobs(Request $request): JsonResponse
    {
        $jobs = null;
        switch ($request->get('status')) {
            case 'active':
                $jobs = $this->jobRepository->findActiveByPosterId($this->user()->id);
                break;
            case 'in_progress':
                if ($this->user()->isCustomer()) {
                    $jobs = $this->jobRepository->findCustomerInPgrogressJobByPosterId($this->user()->id);
                } else {
                    $jobs = $this->jobRepository->findBusinessInProgressJobsByBusinessId($this->user()->business_id);
                }
                break;
            case 'finished':
                if ($this->user()->isCustomer()) {
                    $jobs = $this->jobRepository->findCustomerFinishedJobByPosterId($this->user()->id);
                } else {
                    $jobs = $this->jobRepository->findBusinessFinishedJobsByBusinessId($this->user()->business_id);
                }
                break;
        }

        return response()->json([
            'jobs'      => $jobs ? JobResource::collection($jobs->get()) : [],
            'jobsCount' => $jobs ? $jobs->count() : 0,
        ]);
    }
}
