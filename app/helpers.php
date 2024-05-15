<?php

if (! function_exists('formatStatus')) {
    function formatStatus(int $statusCode): string
    {
        return match (true) {
            ($statusCode >= 400 && $statusCode <= 499) => 'error', // client error
            ($statusCode >= 500 && $statusCode <= 599) => 'fail', // service error
            default => 'success'
        };
    }
}

if (! function_exists('jsonResponse')) {
    function jsonResponse(int $code = 200, $message = '', $data = null, $errors = null)
    {
        $response = [
            'status' => formatStatus($code),
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, substr($code, 0, 3));
    }
}
