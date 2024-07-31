<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Events\GuestMessageSubmitted;
use App\Http\Requests\SendInquiryRequest;

class ContactUsController extends Controller
{
    /**
     * @param SendInquiryRequest $request
     * @return JsonResponse
     */
    public function sendInquiry(SendInquiryRequest $request): JsonResponse
    {
        event(new GuestMessageSubmitted($request->only([
            'name',
            'email',
            'bodyMessage',
        ])));

        return response()->json(['message' => 'Message successfully sent.']);
    }
}
