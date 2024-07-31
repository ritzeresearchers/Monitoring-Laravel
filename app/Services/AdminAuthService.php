<?php

namespace App\Services;

use App\Mail\AdminAuthCode;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Tymon\JWTAuth\JWTAuth;

class AdminAuthService
{
    private JWTAuth $JWTAuth;

    public function __construct(JWTAuth $JWTAuth)
    {
        $this->JWTAuth = $JWTAuth;
    }

    public function verifyCredentials(array $credentials): ?User
    {
        if (!$token = $this->JWTAuth->attempt($credentials)) {
            return null;
        }

        /** @var User $user */
        $user = $this->JWTAuth->setToken($token)->toUser();

        if ($user->user_type !==  config('constants.accountType.admin')) {
            return null;
        }

        return $user;
    }

    public function generateAndSendCode(User $user): void
    {
        DB::beginTransaction();

        try {
            $code = strtoupper(Str::random(4));
            $user->admin_auth_code = $code;
            $user->save();

            $this->sendVerificationCodeSMS($code, $user->email);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function sendVerificationCodeSMS(string $code, string $email): void
    {
        try {
            Mail::to($email)->send(
                new AdminAuthCode($code)
            );
        } catch (\Exception $e) {
            throw new RuntimeException("Error sending email with verification code.", 0, $e);
        }
    }

    public function loginAdmin(string $code): array
    {
        $admin = User::where('admin_auth_code', $code)->firstOr(function () {
            throw new RuntimeException('Code wrong try again!', Response::HTTP_FORBIDDEN);
        });

        $admin->admin_auth_code = null;
        $admin->save();

        $token = $this->JWTAuth->fromUser($admin);

        Auth::guard('api')->setUser($admin);

        return ['token' => $token, 'admin' => $admin];
    }

    public function loginNotProdAdmin(User $admin): array
    {
        $token = $this->JWTAuth->fromUser($admin);

        Auth::guard('api')->setUser($admin);

        return ['token' => $token, 'admin' => $admin];
    }
}
