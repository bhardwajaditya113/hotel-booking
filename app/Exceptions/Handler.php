<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            try {
                if (app()->environment('production')) {
                    $requestId = null;
                    try {
                        $requestId = request()->header('x-railway-request-id');
                    } catch (\Throwable $inner) {
                        // ignore if request not available
                    }

                    \Illuminate\Support\Facades\Log::channel('errorlog')->error('Unhandled exception', [
                        'exception' => (string) $e,
                        'path' => optional(request())->path(),
                        'method' => optional(request())->method(),
                        'request_id' => $requestId,
                        'input_keys' => array_keys(optional(request())->except(['password','password_confirmation','current_password']) ?? []),
                    ]);
                }
            } catch (\Throwable $logEx) {
                // avoid throwing from the exception handler
            }
        });
    }
}
