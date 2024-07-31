<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Http\Resources\UserResource;
use App\Services\Strategy\Deleting\DeleteBusinessStrategy;
use App\Services\Strategy\Deleting\DeleteCustomerStrategy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateEmailRequest;
use App\Http\Requests\ConfirmUpdateEmailRequest;
use App\Events\UpdateEmail;
use App\Events\UpdatePhoneNumber;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use function config;

class UserController extends Controller
{
    use ControllerHelpers;

    private UserRepository $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return UserResource
     */
    public function updateUser(Request $request): UserResource
    {
        $payload = $request->only([
            'name',
            'first_name',
            'last_name',
        ]);

        if ($request->file('avatar')) {
            $imagePath = $request->file('avatar')->store('avatars', 's3');
            $payload['avatar'] = config('config.assetsBaseUrl') . "{$imagePath}";
        }

        $this->user()->update($payload);

        return UserResource::make($this->user());
    }

    /**
     * @param UpdateEmailRequest $request
     * @return JsonResponse
     */
    public function updateEmail(UpdateEmailRequest $request): JsonResponse
    {
        $user = Auth::user();
        $existingUser = User::firstWhere('email', $request->email);

        if ($existingUser && $user->email !== $existingUser->email) {
            return $this->respondError('Email is already in used.');
        }

        if ($request->get('email') !== $request->get('confirmedEmail')) {
            return $this->respondError('Email did not match.');
        }

        $verificationCode = generateRandomString(6);
        $this->user()->update(['verification_code' => $verificationCode]);

        event(new UpdateEmail([
            'email'             => $request->get('email'),
            'verification_code' => $verificationCode,
            'name'              => $this->user()->name,
        ]));

        return response()->json();
    }

    /**
     * @param ConfirmUpdateEmailRequest $request
     * @return JsonResponse
     */
    public function confirmUpdateEmail(ConfirmUpdateEmailRequest $request): JsonResponse
    {
        $user = Auth::user();
        $existingUser = User::firstWhere('email', $request->email);

        if ($existingUser && $user->email !== $existingUser->email) {
            return $this->respondError('Email is already in used.');
        }

        if ($request->get('email') !== $request->get('confirmedEmail')) {
            return $this->respondError('Email did not match.');
        }
        if ($this->user()->verification_code !== $request->get('verificationCode')) {
            return $this->respondError('Invalid verification code.');
        }

        $this->user()->update([
            'email'             => $request->get('email'),
            'verification_code' => '',
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
        $user = Auth::user();
        $existingUser = User::firstWhere('mobile_number', $request->mobile_number);

        if ($existingUser && $user->mobile_number !== $existingUser->mobile_number) {
            return $this->respondError('Mobile number is already in used.');
        }

        if ($error = $this->getMobileNumberValidationError($request->get('mobile_number'))) {
            return $this->respondError($error);
        }

        $verificationCode = generateRandomNumber();
        $this->user()->update(['mobile_number_verification_code' => $verificationCode]);

        event(new UpdatePhoneNumber([
            'mobile_number'                   => $request->get('mobile_number'),
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
        $user = Auth::user();
        $existingUser = User::firstWhere('mobile_number', $request->mobile_number);

        if ($existingUser && $user->mobile_number !== $existingUser->mobile_number) {
            return $this->respondError('Mobile number is already in used.');
        }

        if ($error = $this->getMobileNumberValidationError($request->get('mobile_number'))) {
            return $this->respondError($error);
        }
        if ($this->user()->mobile_number_verification_code != $request->get('verificationCode')) {
            return $this->respondError('Invalid verification code.');
        }

        $this->user()->update([
            'mobile_number'                   => $request->get('mobile_number'),
            'mobile_number_verification_code' => '',
            'mobile_number_verified_at'       => now(),
        ]);

        return response()->json();
    }

    /**
     * @param string $email
     * @return JsonResponse
     */
    public function isEmailRegistered(string $email): JsonResponse
    {
        return response()->json(
            User::where('email', $email)->whereNull('deleted_at')->exists() ||
            Business::where('email', $email)->whereNull('deleted_at')->exists()
        );
    }

    /**
     * @param string $phone
     * @return JsonResponse
     */
    public function isPhoneRegistered(string $phone): JsonResponse
    {
        return response()->json(
            User::where('mobile_number', $phone)->whereNull('deleted_at')->exists() ||
            Business::where('mobile_number', $phone)->whereNull('deleted_at')->exists()
        );
    }

    public function deleteAccount(User $user): JsonResponse
    {
        if ($user->business()->exists()) {
            $user->business()->first()->delete();
        }

        $user->delete();

        return response()->json(['status' => 'success'], ResponseAlias::HTTP_NO_CONTENT);
    }
}
