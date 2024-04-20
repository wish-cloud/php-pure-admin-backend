<?php

namespace App\Http\Controllers;

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
            $expires = now()->addMinutes(config('auth.expiration', 60 * 24 * 7));
            $token = $user->createToken('token', ['*'], $expires);

            return $this->success([
                'account' => $account,
                'roles' => ['admin'],
                'accessToken' => $token->plainTextToken,
                'expires' => $expires->toDateTimeString(),
            ]);
        } else {
            return $this->fail('账号或密码错误', 401);
        }
    }

    public function profile()
    {
        $userData = auth()->user();

        return $this->success($userData);
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return $this->success();
    }
}
