<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function __get($field)
    {
        if ($field === 'version') {
            return request()->attributes->get('version');
        }
        if ($field === 'platform') {
            return request()->attributes->get('platform');
        }
        if ($field === 'roles_secret') {
            return request()->attributes->get('roles_secret');
        }
        if ($field === 'token_expires') {
            return request()->attributes->get('token_expires');
        }
    }

    public function fail(string $message = '', $code = 500, $errors = null)
    {
        return jsonResponse($code, $message, null, $errors);
    }

    public function success($data = [], string $message = '', $code = 200)
    {
        //TODO 登录状态下对比更新用户信息,附加 userInfo 到 data
        //附加 token_expires 到 data
        if ($this->token_expires) {
            $data['token_expires'] = $this->token_expires->toDateTimeString();
        }

        return jsonResponse($code, $message, $data);
    }
}
