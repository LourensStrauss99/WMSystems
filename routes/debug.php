<?php

use Illuminate\Support\Facades\Route;

Route::get('/debug-domain', function () {
    return response()->json([
        'request_host' => request()->getHost(),
        'request_url' => request()->url(),
        'server_name' => $_SERVER['SERVER_NAME'] ?? 'not set',
        'http_host' => $_SERVER['HTTP_HOST'] ?? 'not set',
        'all_headers' => request()->headers->all(),
    ]);
});
