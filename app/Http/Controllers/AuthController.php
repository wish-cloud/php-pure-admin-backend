<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $account = $request->input('account');
        $user = User::where('email', $account)->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            $expires = now()->addMinutes(config('auth.token_expiration', 60 * 12));
            $token = $user->createToken('token', ['*'], $expires);
            $refresh_token = $user->createToken('refresh_token', ['*'], now()->addMinutes(config('auth.refresh_token_expiration', 60 * 24 * 7)));

            return $this->success([
                'account' => $account,
                'roles' => ['admin'],
                'accessToken' => $token->plainTextToken,
                'refreshToken' => $refresh_token->plainTextToken,
                'tokenExpires' => $expires->getPreciseTimestamp(3),
            ]);
        } else {
            return $this->fail('账号或密码错误', 402);
        }
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return $this->success();
    }

    public function refreshToken(Request $request)
    {
        $token = $request->input('refreshToken');
        if (! $token) {
            return $this->fail('授权失败', 401);
        }

        if (strpos($token, '|') === false) {
            $instance = PersonalAccessToken::query()->where('token', hash('sha256', $token))->first();
        } else {
            [$id, $token] = explode('|', $token, 2);
            $instance = PersonalAccessToken::query()->find($id);
        }

        if ($instance && $instance->name == 'refresh_token' && hash_equals($instance->token, hash('sha256', $token)) && $instance->expires_at > now()) {
            $user = User::find($instance->tokenable_id);
            $instance->delete();
            // 创建新Token
            $expires = now()->addMinutes(config('auth.token_expiration', 60 * 12));
            $token = $user->createToken('token', ['*'], $expires);
            $refresh_token = $user->createToken('refresh_token', ['*'], now()->addMinutes(config('auth.refresh_token_expiration', 60 * 24 * 7)));

            return $this->success([
                'accessToken' => $token->plainTextToken,
                'refreshToken' => $refresh_token->plainTextToken,
                'tokenExpires' => $expires->getPreciseTimestamp(3),
            ]);
        } else {
            return $this->fail('授权失败', 401);
        }
    }
}
