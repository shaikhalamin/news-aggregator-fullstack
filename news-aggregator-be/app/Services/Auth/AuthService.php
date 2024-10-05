<?php

namespace App\Services\Auth;

use App\Services\User\UserService;
use Illuminate\Support\Carbon;

class AuthService
{

    public function __construct(private UserService $userService)
    {
    }

    public function createUserToken($user)
    {
        $accessTokenTime = Carbon::now()->addMinute(5);
        $refreshTokenTime = Carbon::now()->addDays(7);
        $accessToken = $user->createToken($user->email, ['expires_at' => $accessTokenTime])->plainTextToken;
        $refreshToken = $user->createToken($user->email, ['expires_at' => $refreshTokenTime])->plainTextToken;

        $this->userService->updateRefreshToken($user->id, $refreshToken);

        $userToken = [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'access_token_expires_at' => $accessTokenTime->getTimestamp(),
            'refresh_token_expires_at' => $refreshTokenTime->getTimestamp(),
            'user' => $user,
        ];

        return $userToken;
    }

    public function reGenerateUserToken($token)
    {
        $user =  $this->userService->findByRefreshToken($token);
        if (is_null($user)) {
            return null;
        }

        return  $this->createUserToken($user);
    }
}
