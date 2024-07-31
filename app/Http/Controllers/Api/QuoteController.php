<?php

namespace App\Http\Controllers\Api;

use App\Events\NewMessage;
use App\Events\NewMessageSent;
use App\Events\QuoteAccepted;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Http\Resources\QuoteResource;
use App\Models\Job;
use App\Models\Lead;
use App\Models\Quote;
use App\Repositories\QuoteRepository;
use App\Services\MessageService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function config;

class QuoteController extends Controller
{
    private QuoteRepository $quoteRepository;

    /**
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(QuoteRepository $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param Request $request
     * @param MessageService $messageService
     * @return QuoteResource|JsonResponse
     * @throws Exception
     */
    public function postQuote(
        Request        $request,
        MessageService $messageService
    )
    {
        $message = $request->get('message', 'Quote sent.');
        if (!$business = $this->user()->business) {
            return $this->respondError('Business not found.');
        }
        if (!$business->is_active || !$business->is_subscription_active) {
            return $this->respondError('Business account is not active.');
        }

        $lead = Lead::find($request->leadId);
        if ($lead->business_id !== $business->id) {
            return $this->respondError('You are not the leads owner.');
        }

        if ($lead->job->status === config('constants.jobStatus.cancelled')) {
            return $this->respondError('Sorry, job is canceled.');
        }

        $lead->update(['has_quoted' => 1]);

        $quote = Quote::firstWhere('lead_id', $request->leadId);
        if (!$quote) {
            $quote = Quote::create(array_merge($request->only([
                'rate_type',
                'cost',
                'currency',
                'comments'
            ]), [
                'lead_id'     => $request->leadId,
                'job_id'      => $lead->job_id,
                'business_id' => $business->id,
            ]));
        }

        $messageThread = $lead->messageThreads()->first();
        if ($messageThread) {
            $messageThread->update([
                'quote_id'     => $quote->id,
                'last_message' => $message,
            ]);
        }
        $messageService->createThread([
            'lead_id'      => $request->leadId,
            'job_id'       => $lead->job_id,
            'customer_id'  => $lead->job->poster_id,
            'sender_id'    => $this->user()->id,
            'business_id'  => $business->id,
            'quote_id'     => $quote->id,
            'last_message' => $message,
        ]);
        $messageService->sendText($this->user()->id, $message, $this->user()->user_type);

        return QuoteResource::make($quote);
    }

    /**
     * @param Quote $quote
     * @return JsonResponse
     * @throws \Throwable
     */
    public function acceptQuote(Quote $quote): JsonResponse
    {
        try {
            DB::beginTransaction();

            if ($quote->job->status !== config('constants.jobStatus.active')) {
                return $this->respondError('Job is not active.');
            }
            if ($quote->job->getAcceptedQuote()) {
                return $this->respondError('There is already an accepted quotation.');
            }

            $quote->update([
                'is_accepted'       => 1,
                'status'            => config('constants.quoteStatus.accepted'),
                'accepted_deadline' => $quote->deadlineAdjustments()->exists() ? $quote->deadlineAdjustments()->first() : $quote->job->target_completion_datetime,
            ]);

            $quote->job()->update([
                'hired_business_id' => $quote->business_id,
                'hired_datetime'    => now(),
                'status'            => config('constants.jobStatus.inProgress'),
            ]);

            QuoteAccepted::dispatch(
                $quote->job->quotes()
                    ->whereNull('is_accepted')
                    ->where('is_cancelled', 0)
                    ->get()
            );

            DB::commit();
            return response()->json();
        } catch (\Throwable $throwable) {
            DB::rollBack();
            throw new \RuntimeException($throwable->getMessage(), $throwable->getCode());
        }
    }

    /**
     * @return JsonResponse
     */
    public function getQuotes(): JsonResponse
    {
        $user = $this->user();
        if ($user->isBusiness()) {
            $quotes = $this->quoteRepository->findQuotedLeadsByBusinessId($user->business_id);
        } else {
            $quotes = $this->quoteRepository->findQuotedJobsByPosterId($user->id);
        }

        return response()->json([
            'quotes' => QuoteResource::collection($quotes),
            'count'  => $quotes->count(),
        ]);
    }

    /**
     * @param Quote $quote
     * @param MessageService $messageService
     * @return JsonResponse
     */
    public function cancelQuote(Quote $quote, MessageService $messageService): JsonResponse
    {
        $quote->update([
            'is_cancelled' => 1,
            'status'       => config('constants.quoteStatus.cancelled')
        ]);

        $result = new MessageResource(
            $messageService
                ->setMessageThread($quote->messageThread)
                ->sendText(
                    $this->user()->id,
                    'Quote canceled.',
                    $this->user()->user_type
                )
        );

        $recipient = $quote->messageThread
            ->participants()
            ->where('participant_id', '!=', $this->user()->id)
            ->first();

        broadcast(new NewMessage($quote->messageThread->id, $result))->toOthers();
        broadcast(new NewMessageSent($recipient->participant_id, $result))->toOthers();

        return response()->json();
    }

    /**
     * @param Job $job
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function getJobResponses(
        Job     $job,
        Request $request
    ): AnonymousResourceCollection
    {
        return QuoteResource::collection($job->getPaginatedQuotes($request->input('pageIndex', 1)));
    }
}
