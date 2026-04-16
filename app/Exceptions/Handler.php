<?php

namespace App\Exceptions;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     */
    protected $levels = [];

    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        // Always return JSON for API routes
        if ($request->is('api/*') || $request->expectsJson()) {
            $statusCode = method_exists($exception, 'getStatusCode')
                ? $exception->getStatusCode()
                : 500;

            return response()->json([
                'status'  => 'error',
                'message' => $exception->getMessage() ?: 'Server error.',
                'debug'   => config('app.debug') ? $exception->getTraceAsString() : null,
            ], $statusCode);
        }

        return parent::render($request, $exception);
    }
}