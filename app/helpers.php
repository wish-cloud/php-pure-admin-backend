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
