<?php

namespace App\Http\Controllers\Api;

use App\Events\BusinessWasAcceptedForJob;
use App\Events\NewMessage;
use App\Events\NewMessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\MessageThreadResource;
use App\Models\Bargain;
use App\Models\DeadlineAdjustment;
use App\Models\Message;
use App\Models\MessageThread;
use App\Models\Quote;
use App\Models\ThreadParticipant;
use App\Repositories\BusinessRepository;
use App\Repositories\MessageRepository;
use App\Repositories\MessageThreadRepository;
use App\Repositories\QuoteRepository;
use App\Repositories\ThreadParticipantRepository;
use App\Services\Factory\MessageFactory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function config;

class MessageController extends Controller
{
    use ControllerHelpers;

    private MessageThreadRepository $messageThreadRepository;
    private ThreadParticipantRepository $threadParticipantRepository;
    private QuoteRepository $quoteRepository;
    private BusinessRepository $businessRepository;
    private MessageRepository $messageRepository;

    /**
     * @param MessageThreadRepository $messageThreadRepository
     * @param ThreadParticipantRepository $threadParticipantRepository
     * @param MessageRepository $messageRepository
     * @param BusinessRepository $businessRepository
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        MessageThreadRepository      $messageThreadRepository,
        ThreadParticipantRepository  $threadParticipantRepository,
        MessageRepository            $messageRepository,
        BusinessRepository           $businessRepository,
        QuoteRepository              $quoteRepository
    )
    {
        $this->messageThreadRepository = $messageThreadRepository;
        $this->threadParticipantRepository = $threadParticipantRepository;
        $this->messageRepository = $messageRepository;
        $this->businessRepository = $businessRepository;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param MessageThread $messageThread
     * @return JsonResponse
     */
    public function deleteThread(MessageThread $messageThread): JsonResponse
    {
        $messageThread->delete();

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getThreads(Request $request): JsonResponse
    {
        $threadsIdList = $this
            ->user()
            ->threadParticipants()
            ->limit(30)
            ->offset(($request->input('pageIndex', 1) - 1) * 30)
            ->pluck('thread_id')
            ->toArray();

        $threads = MessageThread::whereIn('id', $threadsIdList)
            ->with(['job', 'customer', 'business', 'quote'])
            ->get([
                'id',
                'job_id',
                'quote_id',
                'last_message',
                'updated_at',
                'business_id',
                'customer_id',
            ]);

        $userId = $this->user()->id;

        $updatedThreads = $threads->map(function ($thread) use ($userId) {
            $lastRead = $this->threadParticipantRepository->lastRead($thread->id, $userId);

            $thread->setAttribute('lastRead', $lastRead);
            $thread->setAttribute('business', $thread->business);
            $thread->setAttribute('quote', $thread->quote);

            return $thread;
        });

        return response()->json([
            'threads'      => MessageThreadResource::collection($updatedThreads),
            'threadsCount' => $this->user()->threadParticipants()->count(),
        ]);
    }

    /**
     * @param MessageThread $messageThread
     * @return JsonResponse
     */
    public function getThread(MessageThread $messageThread): JsonResponse
    {
        $user = $this->user();

        if ($user->isCustomer()) {
            $thread = $this->messageThreadRepository->findByThreadIdAndUserId($messageThread->id, $user->id);
        } else {
            $thread = $this->messageThreadRepository->findByThreadIdAndBusinessId($messageThread->id, $user->business_id);
        }

        if ($thread) {
            $lastRead = $this->threadParticipantRepository->lastRead($thread->id, $user->id);
            $thread['lastRead'] = $lastRead;
            return response()->json(MessageThreadResource::make($thread));
        }

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function searchMessage(Request $request): JsonResponse
    {
        if (!$request->searchKey) {
            return $this->respondError('Missing search key.');
        }

        $user = $this->user();
        $threads = $this->messageThreadRepository->search($request->searchKey, $user->id);
        $threadsCount = $this->messageThreadRepository->countSearch($request->searchKey, $user->id);
        $updatedThreads = $threads->map(function ($thread) use ($user) {
            $lastRead = $this->threadParticipantRepository->lastRead($thread->id, $user->id);
            $business = $this->businessRepository->findById($thread->business_id);
            $quote = isset($thread->quote_id) ? Quote::find($thread->quote_id) : null;
            $thread->setAttribute('lastRead', $lastRead);
            $thread->setAttribute('business', $business);
            $thread->setAttribute('quote', $quote);
            return $thread;
        });

        return response()->json([
            'threads'      => MessageThreadResource:: collection($updatedThreads),
            'threadsCount' => $threadsCount,
        ]);
    }

    /**
     * @param MessageThread $messageThread
     * @param Request $request
     * @return JsonResponse
     */
    public function getThreadMessages(
        MessageThread $messageThread,
        Request       $request
    ): JsonResponse
    {
        $user = $this->user();

        $isParticipant = $this->threadParticipantRepository->isParticipant($messageThread->id, $user->id);
        if (!$isParticipant) {
            return $this->respondError();
        }

        $messages = $this->messageRepository->findByThreadId($messageThread->id, $request->input('pageIndex', 1));
        ThreadParticipant::where('thread_id', $messageThread->id)
            ->where('participant_id', $user->id)
            ->update(['last_read' => now()]);

        return response()->json([
            'message'      => MessageResource::collection($messages),
            'messageCount' => $messageThread->messages()->count(),
        ]);
    }

    /**
     * @param MessageThread $messageThread
     * @param Request $request
     * @return JsonResponse
     */
    public function getThreadImages(
        MessageThread $messageThread,
        Request       $request
    ): JsonResponse
    {
        $isParticipant = $this->threadParticipantRepository->isParticipant($messageThread->id, $this->user()->id);
        if (!$isParticipant) {
            return $this->respondError();
        }

        return response()->json([
            'images'      => $this->messageRepository->findImagesByThreadId($messageThread->id, $request->input('pageIndex', 1)),
            'imagesCount' => $messageThread
                ->messages()
                ->where('media_link', '!=', '')
                ->count(),
        ]);
    }

    /**
     * @param MessageRequest $request
     * @return JsonResponse
     */
    public function postMessage(MessageRequest $request): JsonResponse
    {
        $user = $this->user();
        $thread = MessageThread::find($request->threadId);

        if (!$thread->job) {
            return $this->respondError('Job not found.');
        }

        if (!$this->threadParticipantRepository->isParticipant($request->threadId, $user->id)) {
            return $this->respondError('You are not the owner of this thread.');
        }

        if (!in_array($request->get('messageType'), config('constants.messageTypes'))) {
            return $this->respondError('Unknown message type.');
        }

        $otherParticipant = ThreadParticipant::where('thread_id', $request->threadId)
            ->where('participant_id', '!=', $user->id)
            ->first();

        switch ($request->get('messageType')) {
            case config('constants.messageTypes.text'):
                $message = $this->storeMessage(
                    $request->messageType,
                    $request->threadId,
                    $request->get('message')
                );
                break;
            case config('constants.messageTypes.uploadImage'):
                $image = $request->file('image');
                if (empty($image))
                    return $this->respondError('Image field is required.');

                $imageUrl = $this->storeFileToS3($image, "messages/{$user->user_type}/$user->id");
                $message = $this->storeMessage(
                    $request->messageType,
                    $request->threadId,
                    $request->get('message'),
                    $imageUrl
                );
                break;
            case config('constants.messageTypes.uploadFile'):
                $file = $request->file('file');
                if (empty($file))
                    return $this->respondError('File field is required.');

                $filename = $request->file('file')->getClientOriginalName();
                $fileUrl = $this->storeFileToS3($file, "messages/{$user->user_type}/$user->id");
                $message = $this->storeMessage(
                    $request->messageType,
                    $request->threadId,
                    $request->message,
                    $fileUrl,
                    $filename
                );
                break;
            case config('constants.messageTypes.calendarScheduling'):
                if (!isset($request->adjustmentDatetime)) {
                    return $this->respondError('Adjustment datetime field is required.');
                }

                $adjustmentDatetime = Carbon::parse($request->adjustmentDatetime)->format('Y-m-d H:i:s');

                $message = $this->storeMessage(
                    $request->messageType,
                    $request->threadId,
                    "$user->user_name requested to change the deadline this date {$adjustmentDatetime}"
                );

                $dAPayload = [
                    'sender_id'           => $user->id,
                    'sender_business_id'  => $user->business_id,
                    'adjustment_datetime' => $adjustmentDatetime,
                    'message_id'          => $message->id,
                    'quote_id'            => $thread->quote->id,
                    'job_id'              => $thread->job->id,
                    'status'              => config('constants.deadlineAdjustmentStatus.pending'),
                ];
                if ($user->isCustomer()) {
                    $dAPayload['sender_business_id'] = null;
                }
                DeadlineAdjustment::create($dAPayload);
                break;
            case config('constants.messageTypes.startProject'):
                if (!$user->isBusiness()) {
                    return $this->respondError('This action is only for service provider account.');
                }

                if ($thread->quote->job->getAcceptedQuote()) {
                    return $this->respondError('There is already an accepted quotation.');
                }

                $quotePayload = [
                    'is_accepted' => 1,
                    'status' => config('constants.quoteStatus.accepted')
                ];

                $adjustment = $thread->quote->accepted_deadline_adjustment;
                if ($adjustment) {
                    $quotePayload['accepted_deadline'] = $adjustment->adjustment_datetime;
                } else {
                    $quotePayload['accepted_deadline'] = $thread->job->target_completion_datetime;
                }

                $quotePayload['cost'] = $thread->job->cost;
                $quotePayload['rate_type'] = $thread->job->rate_type;

                $thread->quote->update($quotePayload);
                $thread->job->update([
                    'hired_business_id' => $thread->quote->business_id,
                    'hired_datetime'    => now(),
                    'status'            => config('constants.jobStatus.inProgress'),
                ]);

                $message = $this->storeMessage(
                    $request->messageType,
                    $request->threadId,
                    "Quote accepted"
                );
                break;
            case config('constants.messageTypes.changeQuoteStatus'):
                if ($request->status === config('constants.quoteStatus.accepted') && $thread->quote->id !== null) {
                    if ($thread->quote->job->getAcceptedQuote()) {
                        return $this->respondError('There is already an accepted quotation.');
                    }

                    $quotePayload = [
                        'is_accepted' => 1,
                        'status'      => config('constants.quoteStatus.accepted')
                    ];

                    $adjustment = $thread->quote->accepted_deadline_adjustment;
                    if ($adjustment) {
                        $quotePayload['accepted_deadline'] = $adjustment->adjustment_datetime;
                    } else {
                        $quotePayload['accepted_deadline'] = $thread->job->target_completion_datetime;
                    }

                    $bargain = $thread->quote->bargain;
                    if ($bargain) {
                        $quotePayload['cost'] = $bargain->cost;
                        $quotePayload['rate_type'] = $bargain->rate_type;
                    }

                    $thread->quote->update($quotePayload);
                    $thread->job->update([
                        'hired_business_id' => $thread->quote->business_id,
                        'hired_datetime'    => now(),
                        'status'            => config('constants.jobStatus.inProgress')
                    ]);
                    $message = $this->storeMessage(
                        $request->messageType,
                        $request->threadId,
                        "$user->user_name {$request->status} the quotation."
                    );

                    BusinessWasAcceptedForJob::dispatch([
                        'user_id'     => $otherParticipant->user->id,
                        'business_id' => $thread->quote->business_id,
                        'first_name'  => $otherParticipant->user->first_name,
                        'last_name'   => $otherParticipant->user->last_name
                    ]);

                } elseif ($request->status === config('constants.quoteStatus.notInterested')) {
                    $thread->quote->update([
                        'is_cancelled' => 1,
                        'status'       => $request->status,
                    ]);

                    if($this->user()->isCustomer()) {
                        $type =  MessageFactory::CUSTOMER;
                        $name = $this->user()->name;
                    } else {
                        $type =  MessageFactory::BUSINESS;
                        $name = $this->user()->business->name;
                    }

                    $message = $this->storeMessage(
                        $request->messageType,
                        $request->threadId,
                        MessageFactory::createMessage($type, $name, false)
                    );
                } elseif ($request->status === config('constants.quoteStatus.declined')) {
                    $thread->quote->update([
                        'is_cancelled' => 1,
                        'status'       => $request->status,
                    ]);

                    $message = $this->storeMessage(
                        $request->messageType,
                        $request->threadId,
                        "$user->user_name declined the quote."
                    );
                } elseif ($request->status === config('constants.quoteStatus.cancelled')) {
                    $thread->quote->update([
                        'is_cancelled' => 1,
                        'status'       => $request->status,
                    ]);

                    $message = $this->storeMessage(
                        $request->messageType,
                        $request->threadId,
                        "Quote is canceled."
                    );
                }
                break;
            case config('constants.messageTypes.bargain'):
                if ($user->isCustomer()) {
                    return $this->respondError('Only the service provider can execute this acction.');
                }

                $message = $this->storeMessage(
                    $request->messageType,
                    $request->threadId, 'Quote ammended.'
                );

                Bargain::create([
                    'message_id'  => $message->id,
                    'quote_id'    => $thread->quote->id,
                    'job_id'      => $thread->job->id,
                    'business_id' => $user->business_id,
                    'rate_type'   => $request->rate_type,
                    'user_id'     => $user->id,
                    'cost'        => $request->cost,
                    'status'      => config('constants.bargainStatus.pending'),
                ]);
                break;
            case config('constants.messageTypes.bargainAcceptance'):
                if ($user->isBusiness()) {
                    return $this->respondError('This action is only for customer account.');
                }

                if ($request->status === config('constants.quoteStatus.accepted')) {
                    $lastMessage = "$user->user_name has approved the amended quote for this job. The job will begin once $user->user_name has accepted the quote.";
                } elseif ($request->status === config('constants.quoteStatus.rejected')) {
                    $lastMessage = "$user->user_name has rejected the new quote for this job. Further discussion may be required, otherwise either party may opt out of this quote by selecting ‘Not Interested’ on the right-side menu.";
                }

                $message = $this->storeMessage($request->messageType, $request->threadId, $lastMessage);

                if (!$thread->quote->bargain) {
                    return $this->respondError('No bargain submitted.');
                }

                Bargain::where('quote_id', $thread->quote->id)
                    ->orderBy('created_at', 'desc')
                    ->first()
                    ->update($request->only(['status']));
                break;
            case config('constants.messageTypes.startProjectOptionResult'):
            case config('constants.messageTypes.bargainCostEstimate'):
            case config('constants.messageTypes.bargainRateTypeResult'):
            case config('constants.messageTypes.bargainCostEstimateResult'):
            case config('constants.messageTypes.changeDeadline'):
            case config('constants.messageTypes.calendarSchedulingResponse'):
                break;
        }

        if (!isset($message)) {
            $message = $this->storeMessage($request->messageType, $request->threadId, $request->get('message'));
        }

        $this->threadParticipantRepository->updateParticipantLastReadStatus($request->threadId, $user->id);

        MessageThread::find($request->threadId)->update(['last_message' => $message->text]);

        $result = new MessageResource($message);
        broadcast(new NewMessage($request->threadId, $result))->toOthers();
        broadcast(new NewMessageSent($otherParticipant->participant_id, $result))->toOthers();

        return response()->json($result);
    }

    /**
     * @param $messageType
     * @param $threadId
     * @param $msg
     * @param string $fileUrl
     * @param string $fileName
     * @return mixed
     */
    public function storeMessage(
        $messageType,
        $threadId,
        $msg,
        string $fileUrl = '',
        string $fileName = ''
    )
    {
        $user = $this->user();
        $messagePayload = [
            'sender_id'    => $user->id,
            'thread_id'    => $threadId,
            'message_type' => $messageType,
            'text'         => $msg,
            'media_link'   => $fileUrl,
            'media_name'   => $fileName,
        ];
        if ($user->isCustomer()) {
            unset($messagePayload['sender_business_id']);
        }

        return Message::create($messagePayload);
    }
}
