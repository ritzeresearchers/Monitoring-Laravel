<?php

namespace App\Http\Controllers\Api;

use App\Events\CustomerUserRegistered;
use App\Events\RequestSecurityCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\Business;
use App\Models\LoginToken;
use App\Models\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateController extends Controller
{
    /**
     * @throws GuzzleException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $input = $request->only('email', 'password');
        if (!$jwt_token = JWTAuth::attempt($input)) {
            return $this->respondError('Invalid Email or Password.');
        }

        $user = User::whereNull('deleted_at')->firstWhere('email', $request->get('email'));

        if($user->user_type === config('constants.accountType.admin')) {
            return $this->respondError('Unable to authorize this user.');
        }

        $business = Business::whereNull('deleted_at')->firstWhere('email', $request->get('email'));
        if (!$user->is_active || ($business && !$business->is_active)) {
            return $this->respondError('Your account is suspended. Please contact the support.');
        }

        if (!$user->email_verified_at) {
            $verificationCode = Str::random(10);

            $user->update(['verification_code' => $verificationCode]);

            event(new CustomerUserRegistered([
                'first_name'        => $user->first_name,
                'last_name'         => $user->last_name,
                'email'             => $user->email,
                'verification_code' => $verificationCode,
                'name'              => implode(' ', [$user->first_name, $user->last_name]),
                'user_type'         => $user->user_type,
            ]));

            return $this->respondError('Please check your email to verify your account before logging in.');
        }

        Session::put('login_token', '');
        return response()->json([
            'user'  => UserResource::make($user),
            'token' => $jwt_token,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function impersonateLogin(Request $request): JsonResponse
    {
        $user = User::firstWhere('email', $request->get('email'));
        if ($user && $jwt_token = Auth::fromUser($user)) {
            LoginToken::whereNotNull('id')->delete();

            LoginToken::create([
                'user_id' => $this->user()->id,
                'token'   => $jwt_token,
            ]);

            session(['login_token' => $jwt_token]);
            session()->save();

            Session::put('login_token', $jwt_token);
            Session::save();

            return response()->json(['token' => $jwt_token]);
        }

        return $this->respondError('Invalid login.');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loginBackAsAdmin(Request $request): JsonResponse
    {
        $adminStoredLoginToken = LoginToken::firstWhere('token', $request->bearerToken());

        Log::info('adminStoredLoginToken: ' . $adminStoredLoginToken);

        if ($adminStoredLoginToken) {
            $this->guard()->logout();

            $adminUser = User::find($adminStoredLoginToken->user_id);
            if ($jwt_token = Auth::fromUser($adminUser)) {
                LoginToken::whereNotNull('id')->delete();

                return response()->json(['token' => $jwt_token]);
            }
        }

        return $this->respondError('Invalid login.');
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->guard()->logout();

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getUser(Request $request): JsonResponse
    {
        return response()->json([
            'impersonation' => LoginToken::where('token', $request->bearerToken())->exists(),
            'data'          => UserResource::make($this->user()),
        ]);
    }

    /**
     * @return Guard|StatefulGuard
     */
    private function guard()
    {
        return Auth::guard('api');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        if (!$this->guard()->attempt([
                'email'    => $this->user()->email,
                'password' => $request->get('currentPassword'),
            ])) {
            return $this->respondError('Old password is incorrect.');
        }

        if ($request->get('new_password') !== $request->get('passwordConfirmation')) {
            return $this->respondError('Password did not match.');
        }

        $this->user()->update(['password' => bcrypt($request->get('new_password'))]);

        return response()->json();
    }

    /**
     * @param string $email
     * @return JsonResponse
     */
    public function sendSecurityCode(string $email): JsonResponse
    {
        $securityCode = generateRandomString(6);

        $user = User::firstWhere('email', $email);
        $user->update(['security_code' => $securityCode]);

        event(new RequestSecurityCode([
            'email'         => $email,
            'security_code' => $securityCode,
            'name'          => $user->name ?? $email,
        ]));

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'securityCode' => ['required'], ['exists:users,security_code'],
            'new_password' => ['required'], ['min:6'],
        ]);
        User::firstWhere('security_code', $request->get('securityCode'))->update([
            'security_code' => '',
            'password'      => bcrypt($request->get('new_password')),
        ]);

        return response()->json();
    }
}
