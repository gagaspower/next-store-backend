<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SessionRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SessionController extends Controller
{
    public function create(SessionRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $user = User::byEmail($request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('Lara-Store-Token')->accessToken;

        $auth_data = [
            'id'    => $user->id,
            'name'  => $user->name,
            'roles' => $user->roles,
        ];

        return response()->json([
            'message' => 'Success',
            'data' => [
                'session_id' => $auth_data,
                'token'      => $token
            ]
        ], JsonResponse::HTTP_OK);
    }

    public function destroy()
    {
        Auth::user()
            ->tokens
            ->each(function ($token, $key) {
                $this->revokeAccessAndRefreshTokens($token->id);
            });

        return response()->json(['message' => 'Your session has been destroy!'], JsonResponse::HTTP_OK);
    }

    protected function revokeAccessAndRefreshTokens($tokenId)
    {
        $tokenRepository        = app('Laravel\Passport\TokenRepository');
        $refreshTokenRepository = app('Laravel\Passport\RefreshTokenRepository');

        $tokenRepository->revokeAccessToken($tokenId);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);
    }
}
