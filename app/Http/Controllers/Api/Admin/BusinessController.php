<?php

namespace App\Http\Controllers\Api\Admin;

use App\Events\ResendVerificationLink;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessDocument;
use App\Http\Resources\BusinessResource;
use App\Models\Business;
use App\Models\User;
use App\Repositories\BusinessRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    protected BusinessRepository $businessRepository;

    /**
     * @param BusinessRepository $businessRepository
     */
    public function __construct(BusinessRepository $businessRepository)
    {
        $this->businessRepository = $businessRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getBusinesses(Request $request): JsonResponse
    {
        $params = $request->all();
        $businessesQuery = $this->businessRepository->getBusinessItemsOverview($params);
        $businessesCount = $this->businessRepository->getFilteredModelCount($params);

        return response()->json([
            'businesses'    => BusinessResource::collection($businessesQuery->get()),
            'businessCount' => $businessesCount,
        ]);
    }

    /**
     * @param \App\Models\BusinessDocument $businessDocument
     * @return BusinessDocument
     */
    public function verifyBusinessDocument(\App\Models\BusinessDocument $businessDocument): BusinessDocument
    {
        $businessDocument->update(['is_verified' => 1]);

        return BusinessDocument::make($businessDocument);
    }

    /**
     * @param Request $request
     * @param Business $business
     * @return BusinessResource
     */
    public function updateBusiness(
        Request  $request,
        Business $business
    ): BusinessResource
    {
        $business->user()->update($request->only(['is_active']));
        $business->update($request->only(['is_active']));

        return BusinessResource::make($business);
    }

    public function resendVerificationCode(string $email): JsonResponse
    {
        $user = User::firstWhere('email', $email);

        if (!$user) {
            return $this->respondError('User not found');
        }

        if ($user->email_verified_at) {
            return $this->respondError('User is already verified');
        }

        $user->update([
            'verification_code' => Str::random(10)
        ]);

        event(new ResendVerificationLink([
            'email'             => $email,
            'name'              => $user->name,
            'verification_code' => $user->verification_code,
            'user_type'         => $user->user_type
        ]));

        return response()->json();
    }
}
