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
    }

    public function fail(string $message = '', $code = 500, $errors = null)
    {
        return response()->json([
            'status' => formatStatus($code),
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
        ], substr($code, 0, 3));
    }

    public function success($data = [], string $message = '', $code = 200)
    {
        return response()->json([
            'status' => formatStatus($code),
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
