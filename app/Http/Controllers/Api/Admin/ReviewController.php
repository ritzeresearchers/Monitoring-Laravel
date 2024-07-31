<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Http\Resources\ReviewResource;
use Illuminate\Http\Response;

class ReviewController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getReviews(Request $request): JsonResponse
    {
        $reviews = Review::with(['job', 'business'])
            ->limit(config('config.paginationLimit'))
            ->orderBy('id')
            ->offset(($request->input('pageIndex', 1) - 1) * config('config.paginationLimit'))
            ->get();

        return response()->json([
            'reviews'      => ReviewResource::collection($reviews),
            'reviewsCount' => Review::count(),
        ]);
    }

    /**
     * @param Review $review
     * @return Response
     */
    public function deleteReview(Review $review): Response
    {
        $review->delete();

        return response()->noContent();
    }
}
