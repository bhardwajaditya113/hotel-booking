<?php

use Illuminate\Support\Facades\Route;

// Debug routes - remove in production
Route::get('/debug/session', function () {
    $session = session()->all();
    return response()->json([
        'session_driver' => config('session.driver'),
        'csrf_token' => csrf_token(),
        'session_id' => session()->getId(),
        'session_data' => $session,
    ]);
});

Route::post('/debug/login-test', function () {
    return response()->json([
        'csrf_from_request' => request()->input('_token'),
        'csrf_from_token_helper' => csrf_token(),
        'cookies' => request()->cookies->all(),
        'headers' => collect(request()->headers->all())->except(['host', 'user-agent'])->toArray(),
    ]);
});
