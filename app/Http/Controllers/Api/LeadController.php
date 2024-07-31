<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Lead;
use App\Models\User;
use App\Events\NewMessage;
use Illuminate\Http\Request;
use App\Events\NewMessageSent;
use App\Events\UserRegistered;
use App\Services\AccountService;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\JobResource;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeadResource;
use App\Repositories\LeadRepository;
use App\Http\Requests\SendLeadRequest;
use App\Http\Resources\MessageResource;

class LeadController extends Controller
{
    use ControllerHelpers;

    protected LeadRepository $leadRepository;

    /**
     * @param LeadRepository $leadRepository
     */
    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    /**
     * @param Request $request
     * @param Lead $lead
     * @return JsonResponse
     */
    public function postReport(
        Request $request,
        Lead    $lead
    ): JsonResponse
    {
        $lead->reports()->create([
            'job_id'      => $lead->job_id,
            'reporter_id' => $this->user()->id,
            'message'     => $request->message,
        ]);

        return response()->json();
    }

    /**
     * @return JsonResponse
     */
    public function getLeads(): JsonResponse
    {
        $leads = $this->leadRepository->findByBusinessId($this->user()->business_id);

        return response()->json([
            'leads' => LeadResource::collection($leads),
            'count' => $leads->count(),
        ]);
    }

    /**
     * @param SendLeadRequest $request
     * @return JobResource|JsonResponse
     */
    public function sendLeads(SendLeadRequest $request)
    {
        if ($error = $this->getJobPropertiesValidationError($request)) {
            return $this->respondError($error);
        }

        $user = User::firstWhere('email', $request->get('email'));
        if ($user) {
            if (!$request->get('password')) {
                return $this->respondError('Email is already registered, please login.');
            }
            if ($user->isBusiness()) {
                return $this->respondError('Invalid account type, only customer account can send leads.');
            }
        }

        if (!$user) {
            $password = generateRandomString();
            $payload = [
                'email'             => $request->get('email'),
                'verification_code' => generateRandomString(),
                'name'              => $request->get('name', $request->get('email')),
                'password'          => bcrypt($password),
                'user_type'         => config('constants.accountType.customer'),
            ];

            event(new UserRegistered($payload));
            $user = User::create($payload);
        }

        $account = (new AccountService())->authenticate([
            'email'    => $request->get('email'),
            'password' => $password ?? $request->get('password'),
        ]);
        if (!isset($account['token'])) {
            return $this->respondError();
        }

        $job = $user->jobs()->create(array_merge($request->only([
            'service_id',
            'category_id',
            'title',
            'description',
            'job_type',
            'rate_type',
            'target_job_done',
            'other_details',
        ]), [
            'location_id'                => $request->get('locationId'),
            'target_completion_datetime' => $this->getTargetCompletionDatetime($request),
            'status'                     => config('constants.jobStatus.pending'),
        ]));
        $leads =$job->lead()->create([
            'business_id' => $request->get('business_id'),
            'is_accepted' => 0,
        ]);
        // $job->lead()->create([
        //     'business_id' => $request->get('business_id'),
        //     'is_accepted' => 0,
        // ]);
        Log::info($leads);
      dd($leads);
        return JobResource::make($job);
    }

    /**
     * @param Lead $lead
     * @return JsonResponse
     */
    public function notInterested(Lead $lead): JsonResponse
    {
        if ($lead->business_id !== $this->user()->business_id) {
            return $this->respondError();
        }

        $lead->update(['is_not_interested' => 1]);

        return response()->json();
    }

    /**
     * @param Request $request
     * @param MessageService $messageService
     * @param Lead $lead
     * @return JsonResponse
     * @throws Exception
     */
    public function sendLeadMessage(
        Request $request,
        Lead $lead,
        MessageService $messageService
    ): JsonResponse
    {
        $user = $this->user();

        $messageThread = $messageService->createThread([
            'lead_id'      => $lead->id,
            'job_id'       => $lead->job_id,
            'customer_id'  => $lead->job->poster_id,
            'business_id'  => $user->business_id,
            'sender_id'    => $user->id,
            'quote_id'     => null,
            'last_message' => $request->get('message'),
        ]);
        $result = new MessageResource($messageService->sendText(
            $user->id,
            $request->get('message'),
            $user->user_type
        ));

        broadcast(new NewMessage($messageThread->id, $result))->toOthers();
        broadcast(new NewMessageSent($lead->job->poster_id, $result))->toOthers();

        return response()->json();
    }
}
