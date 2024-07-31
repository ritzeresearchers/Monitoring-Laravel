<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Models\Bargain;
use App\Models\MessageThread;
use App\Models\DeadlineAdjustment;

class MessageResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        $bargain = null;
        if ($this->message_type === config('constants.messageTypes.bargainAcceptance')) {
            $thread = MessageThread::find($this->thread_id);

            $bargain = Bargain::where('quote_id', $thread->quote_id)
                ->orderBy('id', 'desc')
                ->first();
        }

        if ($this->message_type === config('constants.messageTypes.bargain')) {
            $bargain = $this->bargain;
        }

        return [
            'id'                 => $this->id,
            'messageType'        => $this->message_type,
            'threadId'           => $this->thread_id,
            'mediaLink'          => $this->media_link,
            'mediaName'          => $this->media_name,
            'text'               => $this->text,
            'sender'             => UserResource::make($this->sender),
            'senderId'           => $this->sender_id,
            'createdAt'          => $this->created_at,
            'updatedAt'          => $this->updated_at,
            'adjustmentDatetime' => $this->when($this->message_type === config('constants.messageTypes.calendarScheduling'), function() {
                $da = DeadlineAdjustment::firstWhere('message_id', $this->id);

                return Carbon::parse($da->adjustment_datetime)->format('Y-m-d H:i:s');
            }),
            $this->mergeWhen($bargain, [
                'bargain' => BargainResource::make($bargain)
            ]),
        ];
    }
}
