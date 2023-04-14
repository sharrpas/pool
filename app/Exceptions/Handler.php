<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use League\Config\Exception\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'error' => true,
                'code' => 400,
                'message' => "اعتبار سنجی ناموفق بود",
                'data' => $e->errors()
            ], 400);
        }

        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'error' => true,
                'code' => 404,
                'message' => "مدل پیدا نشد",
                'data' => "Model not found. Model: " . $e->getModel() . ", ID: " . implode(', ', $e->getIds())
            ], 404);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'error' => true,
                'code' => 403,
                'message' => "روش مجاز نمی باشد",
                'data' => "Method not allowed:" . $e->getMessage(),
            ], 403);
        }

        if ($e instanceof UnauthorizedException) {
            return response()->json([
                'error' => true,
                'code' => 401,
                'message' => "احراز هویت ناموفق بود",
                'data' => $e->getMessage() ,
            ], 401);
        }

        return parent::render($request, $e);
    }
}
