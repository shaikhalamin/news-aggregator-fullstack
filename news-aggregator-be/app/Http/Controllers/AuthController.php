<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\Auth\AuthService;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as RESPONSE;

class AuthController extends AbstractApiController
{
    public function __construct(private UserService $userService, private AuthService $authService)
    {
    }

    /**
     * Login method.
     *
     * @param  \App\Http\Requests\AuthRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(AuthRequest $request)
    {
        $payload = $request->validated();

        $user = $this->userService->findByEmail($payload['email']);

        if (!$user || !Hash::check($payload['password'], $user->password)) {

            $response = [
                'message' => 'Email or Password did not match !',
                'errors' => []
            ];

            return $this->apiErrorResponse($response, RESPONSE::HTTP_UNAUTHORIZED);
        }

        $loginResult = $this->authService->createUserToken($user);

        return $this->apiSuccessResponse($loginResult, RESPONSE::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $this->authService->logOut($user);
        $response = null;

        return $this->apiSuccessResponse($response, RESPONSE::HTTP_NO_CONTENT);
    }

    /**
     * Refresh the access & refresh token with old refresh token
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        $refreshToken =  $request->header('RefreshToken');

        $userToken = $this->authService->reGenerateUserToken($refreshToken);

        if (is_null($userToken)) {
            $response = [
                'message' => 'Token not found',
                'errors' => []
            ];

            return $this->apiErrorResponse($response, RESPONSE::HTTP_UNAUTHORIZED);
        }

        return $this->apiSuccessResponse($userToken, RESPONSE::HTTP_OK);
    }



    /**
     * Remove the specified resource from storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function user(Request $request)
    {
        $response = [
            'success' => true,
            'data' => $request->user(),
        ];

        return $this->apiSuccessResponse($response, RESPONSE::HTTP_OK);
    }
}
