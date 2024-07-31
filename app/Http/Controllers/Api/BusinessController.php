<?php

namespace App\Http\Controllers\Api;

use Throwable;
use Stripe\Stripe;
use App\Models\User;
use function config;
use App\Models\Business;
use App\Models\Location;
use App\Events\UpdateEmail;
use Illuminate\Support\Str;
use App\Events\UserVerified;
use Illuminate\Http\Request;
use App\Services\UploadService;
use App\Events\UpdatePhoneNumber;
use App\Services\BusinessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\JobRepository;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Events\BusinessUserRegistered;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\BusinessResource;
use App\Repositories\BusinessRepository;
use App\Http\Requests\UpdateEmailRequest;
use GuzzleHttp\Exception\GuzzleException;
use App\Http\Requests\UpdateBusinessRequest;
use App\Http\Requests\RegisterBusinessRequest;
use App\Http\Controllers\Traits\ProcessRequest;
use App\Http\Requests\ConfirmUpdateEmailRequest;
use App\Models\BusinessDocument as BusinessDocumentModel;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BusinessController extends Controller
{
    use ControllerHelpers, ProcessRequest;

    protected BusinessRepository $businessRepository;
    private UserRepository $userRepository;
    private JobRepository $jobRepository;

    /**
     * @param BusinessRepository $businessRepository
     * @param UserRepository $userRepository
     * @param JobRepository $jobRepository
     */
    public function __construct(
        BusinessRepository $businessRepository,
        UserRepository $userRepository,
        JobRepository $jobRepository
    ) {
        $this->businessRepository = $businessRepository;
        $this->userRepository = $userRepository;
        $this->jobRepository = $jobRepository;
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function searchBusiness(Request $request): AnonymousResourceCollection
    {
        $businesses = BusinessService::getNearestBusiness(
            Location::find($request->locationId),
            $request
        );

        return BusinessResource::collection($businesses);
    }

    /**
     * @return JsonResponse
     */
    public function getFeatured(): JsonResponse
    {
        return response()->json(
            Business::active()
                ->with([
                    'categories',
                    'reviews',
                ])
                ->featured()
                ->limit(10)
                ->get()
        );
    }

    /**
     * @param string $code
     * @return JsonResponse
     */
    public function verifyEmail(string $code): JsonResponse
    {
        $user = User::firstWhere('verification_code', $code);

        if (!$user) {
            return $this->respondError('Invalid verification link.');
        }

        $user->update([
            'email_verified_at' => now(),
            'verification_code' => '',
        ]);

        $business = Business::where('email', $user->email)->first();
        if ($business) {
            $business->update(['is_verified' => true]);
        }

        event(new UserVerified($user));

        return response()->json();
    }

    /**
     * @param RegisterBusinessRequest $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function register(RegisterBusinessRequest $request): JsonResponse
    {
        if (User::firstWhere('email', $request->get('email'))) {
            return $this->respondError('Email is already registered.');
        }

        $verificationCode = Str::random(10);
        $account = User::create([
            'title' => $request->title,
            'first_name' => $request->first_name,
            'last_name' => $request->middle_name,
            'middle_name' => $request->last_name,
            'email' => $request->get('email'),
            'mobile_number' => $request->mobile_number,
            'mobile_number_verified_at' => now(),
            'password' => bcrypt($request->get('password')),
            'verification_code' => $verificationCode,
            'user_type' => config('constants.accountType.business'),
        ]);
        /** @var Business $business */
        $business = $account->business()->create([
            'name' => $request->businessName,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'mobile_number_verified_at' => now(),
            'location' => Location::find($request->locationId)->location,
            'lead_location_coverage' => config('constants.leadLocationCoverage.all'),
            'work_categories' => $request->workCategories,
            'services' => $request->services,
        ]);
        // $business->bankDetail()->create([
        //     'post_code' => $request->postcode,
        //     'line1' => $request->addressLine1,
        //     'line2' => $request->addressLine2,
        //     'account_holder_name' => $request->accountHolderName,
        //     'account_number' => $request->accountNumber,
        //     'bank_sort_code' => $request->bankSortCode,
        // ]);

        foreach (config('constants.notificationChannelTypes') as $channelType) {
            $business->notificationChannels()->create(
                ['channel' => $channelType, 'is_enabled' => false]
            );
        }

        foreach (config('constants.notifiableEvents') as $event) {
            $business->notificationChannels()->create(
                ['event' => $event, 'is_enabled' => false]
            );
        }

        if ($request->has('workCategories')) {
            $business->categories()->sync($request->get('workCategories'));
        }
        if ($request->has('services')) {
            $business->services()->sync($request->get('services'));
        }

        // Todo enable this event for mail purpose

        // event(new BusinessUserRegistered([
        
        //     'businessId' => $business->id,
        //     'first_name' => $request->first_name,
        //     'last_name' => $request->last_name,
        //     'email' => $request->get('email'),
        //     'verification_code' => $verificationCode,
        //     'name' => $request->get('name', $request->get('email')),
        //     'user_type' => config('constants.accountType.business'),
        // ]));
        // $this->processSubscription($account,$request->token,$request->plan);
        return response()->json();
    }
    public function processSubscription($user,$token,$plan)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $user->createOrGetStripeCustomer();
        $paymentMethod = \Stripe\PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'token' => $token,
            ],
        ]);
        $payment_method = $user->addPaymentMethod($paymentMethod->id);
        try {
            $user->newSubscription('account_subscription', $plan)
                ->create($payment_method->id, [
                    'email' => $user->email,
                ]);
            return response()->json([
                'success'      => true,
                'message' => "Subscription successful",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'      => false,
                'message' => 'Error proccessing subscription. ' . $e->getMessage()
            ]);
        }
    }
    /**
     * @return BusinessResource
     */
    public function getCurrentBusiness(): BusinessResource
    {
        return BusinessResource::make($this->user()->business);
    }

    /**
     * @param Business|null $business
     * @return BusinessResource
     */
    public function getBusinessById(Business $business): BusinessResource
    {
        return BusinessResource::make($business);
    }

    /**
     * @param UpdateBusinessRequest $request
     * @return JsonResponse
     */
    public function updateBusiness(UpdateBusinessRequest $request): JsonResponse
    {
        $businessPayload = array_merge($request->only([
            'location',
            'mobile_number',
            'description',
            'landline',
            'address',
            'website',
            'name',
        ]), [
            'name' => $request->get('business_name'),
        ]);

        if (!empty($request->file('logo'))) {
            $imagePath = $request->file('logo')->store('logo', 's3');
            $businessPayload['logo'] = config('config.assetsBaseUrl') . "{$imagePath}";
        }

        UploadService::uploadBusinessDocument($request, $this->user()->business);

        if ($request->has('workCategories')) {
            $this->user()->business->categories()->sync($request->get('workCategories'));
        }
        if ($request->has('services')) {
            $this->user()->business->services()->sync($this->processServices($request->get('services')));
        }

        $this->user()->business->update($businessPayload);
        // $this->user()->business->bankDetail->update(array_merge($request->only([
        //     'account_holder_name',
        //     'account_number',
        //     'bank_sort_code',
        // ]), [
        //     'post_code' => $request->get('postcode'),
        //     'line1' => $request->get('address_line1'),
        //     'line2' => $request->get('address_line2'),
        // ]));

        return response()->json();
    }

    public function deleteBusinessDocument(int $documentTypeId): JsonResponse
    {
        $businessId = $this->user()->business_id;

        Storage::delete("document/{$businessId}/document_{$documentTypeId}");

        BusinessDocumentModel::where([
            ['business_id', '=', $businessId],
            ['document_type_id', '=', $documentTypeId]
        ])->delete();

        return response()->json();
    }

    /**
     * @param UpdateEmailRequest $request
     * @return JsonResponse
     */
    public function updateEmail(UpdateEmailRequest $request): JsonResponse
    {
        if ($error = $this->getConfirmationEmailValidationError($request->get('email'), $request->get('confirmedEmail'))) {
            return $this->respondError($error);
        }

        $verificationCode = generateRandomString(6);
        $this->user()->business->update(['email_verification_code' => $verificationCode]);

        event(new UpdateEmail([
            'email' => $request->get('email'),
            'verification_code' => $verificationCode,
            'name' => $this->user()->user_name,
        ]));

        return response()->json();
    }

    /**
     * @param ConfirmUpdateEmailRequest $request
     * @return JsonResponse
     */
    public function confirmUpdateEmail(ConfirmUpdateEmailRequest $request): JsonResponse
    {
        if ($error = $this->getConfirmationEmailValidationError($request->get('email'), $request->get('confirmedEmail'))) {
            return $this->respondError($error);
        }

        if ($this->user()->business->email_verification_code != $request->get('verificationCode')) {
            return $this->respondError('Invalid verification code.');
        }

        $this->user()->business->update([
            'email' => $request->get('email'),
            'email_verification_code' => '',
            'email_verified_at' => now(),
        ]);

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateMobileNumber(Request $request): JsonResponse
    {
        if ($error = $this->getMobileNumberValidationError($request->get('mobile_number'))) {
            return $this->respondError($error);
        }

        $verificationCode = generateRandomNumber();

        $this->user()->business->update(['mobile_number_verification_code' => $verificationCode]);

        event(new UpdatePhoneNumber([
            'mobile_number' => $request->get('mobile_number'),
            'mobile_number_verification_code' => $verificationCode,
        ]));

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmUpdateMobileNumber(Request $request): JsonResponse
    {
        if ($error = $this->getMobileNumberValidationError($request->get('mobile_number'))) {
            return $this->respondError($error);
        }

        if ($this->user()->business->mobile_number_verification_code !== $request->get('verificationCode')) {
            return $this->respondError('Invalid verification code.');
        }

        $this->user()->business->update([
            'mobile_number' => $request->get('mobile_number'),
            'mobile_number_verification_code' => '',
            'mobile_number_verified_at' => now(),
        ]);

        return response()->json();
    }
}
