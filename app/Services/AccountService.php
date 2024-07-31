<?php

namespace App\Services;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AccountService
{
    /**
     * @param $credentials
     * @return array
     */
    public function authenticate($credentials): array
    {
        if (!$jwt_token = JWTAuth::attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'Invalid Email or Password',
            ];
        }

        return [
            'success' => true,
            'user'    => User::where('email', $credentials['email'])->first(),
            'token'   => $jwt_token,
        ];
    }
}
