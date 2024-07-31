<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttemptLoginRequest;
use App\Http\Requests\Admin\AuthCodeVerifyCodeRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AdminAuthService;
use Illuminate\Http\JsonResponse;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class AdminAuthController extends Controller
{
    private AdminAuthService $adminAuthService;

    public function __construct(AdminAuthService $adminAuthService)
    {
        $this->adminAuthService = $adminAuthService;
    }

    public function checkCredentials(AttemptLoginRequest $request): JsonResponse
    {
        try {
            $user = $this->adminAuthService->verifyCredentials($request->all());

            if (!$user) {
                throw new RuntimeException('Wrong credential or user not admin', ResponseAlias::HTTP_FORBIDDEN);
            }

            if (config('constants.applicationEnv.currentEnv') !== config('constants.possibleApplicationEnvs.production')) {
                $adminData = $this->adminAuthService->loginNotProdAdmin($user);
                return response()->json([
                    'admin' => UserResource::make($adminData['admin']),
                    'token' => $adminData['token']
                ],
                    ResponseAlias::HTTP_OK
                );
            }

            $this->adminAuthService->generateAndSendCode($user);

            return response()->json(UserResource::make($user), ResponseAlias::HTTP_OK);
        } catch (RuntimeException $exception) {
            throw new RuntimeException($exception->getMessage(), $exception->getCode());
        }
    }

    public function verifyCodeAndLogIn(AuthCodeVerifyCodeRequest $request): JsonResponse
    {
        try {
            $adminData = $this->adminAuthService->loginAdmin($request->get('code'));
        } catch (Throwable $throwable) {
            throw new RuntimeException($throwable->getMessage(), $throwable->getCode());
        }

        return response()->json([
            'admin' => UserResource::make($adminData['admin']),
            'token' => $adminData['token']
        ],
            ResponseAlias::HTTP_OK
        );
    }

    public function resendVerificationCode(User $admin): JsonResponse
    {
        try {
            $this->adminAuthService->generateAndSendCode($admin);
        } catch (Throwable $throwable) {
            throw new RuntimeException($throwable->getMessage(), $throwable->getCode());
        }

        return response()->json(['status' => 'Success']);
    }
}
// This is the backend test
