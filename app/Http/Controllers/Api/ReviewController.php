<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Business;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReviewController extends Controller
{
    /**
     * @param Request $request
     * @param Business $business
     * @return ReviewResource|JsonResponse
     */
    public function postBusinessReview(
        Request  $request,
        Business $business
    )
    {
        $posterId = $this->user()->id;
        if ($business
            ->reviews()
            ->where('user_id', $posterId)
            ->where('job_id', $request->get('jobId'))
            ->count()
        ) {
            return $this->respondError('You already posted a review for this business.');
        }

        $review = $business->reviews()->create([
            'user_id' => $posterId,
            'job_id'  => $request->get('jobId'),
            'rating'  => $request->get('rating'),
            'content' => $request->get('content'),
        ]);

        return ReviewResource::make($review);
    }

    /**
     * @param Job $job
     * @return AnonymousResourceCollection
     */
    public function getJobReviews(Job $job): AnonymousResourceCollection
    {
        return ReviewResource::collection($job->reviews);
    }

    /**
     * @param StoreJobReviewRequest $request
     * @param Job $job
     * @return ReviewResource|JsonResponse
     */
    public function postJobReview(
        StoreJobReviewRequest $request,
        Job                   $job
    )
    {
        $posterId = $this->user()->id;
        if (!$job->isPoster($posterId)) {
            return $this->respondError('You are not the owner of this job.');
        }

        $review = $job->reviews()->create(array_merge($request->only([
            'business_id',
            'rating',
            'content',
        ]), [
            'user_id' => $posterId,
            'job_id'  => $job->id,
        ]));

        return ReviewResource::make($review);
    }

    /**
     * @param Business $business
     * @return AnonymousResourceCollection
     */
    public function getPostedJobReview(Business $business): AnonymousResourceCollection
    {
        return ReviewResource::collection(
            $business
                ->reviews()
                ->where('user_id', $this->user()->id)
                ->get()
        );
    }
}
