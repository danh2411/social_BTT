<?php

namespace App\Exceptions;

use App\Constants\BaseResponseTypeConstant;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
            if (app()->bound('sentry')) {
                app('sentry')->captureException(
                    $e,
                    \Sentry\EventHint::fromArray(['extra' => ['time' => date('Y-m-d h:i:s')]])
                );
            }
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Token is missing or invalid.',
                'error' => 'Unauthenticated.',
            ], 401);
        }
        // Kiểm tra nếu là AuthenticationException
        if ($e instanceof AuthenticationException) {
                return response()->json([
                    'success' => false,

                    'message' => "Token is missing or invalid",
                    'type' => BaseResponseTypeConstant::RESPONSE_TYPE_ERROR_UNAUTHENTICATED,
                    'error' => 'Token is missing or invalid. Please log in again.'
                ], 401);
        }

        // Xử lý lỗi HttpException đã định nghĩa trước đó
        if ($e instanceof HttpException) {
            $statusCode = (int) $e->getStatusCode();
            $msg = empty($e->getMessage()) ? match ($statusCode) {
                401, 403 => 'You are not authorized to view the requested resource.',
                404 => 'The page you requested does not exist.',
                default => 'An error may have occurred.',
            } : $e->getMessage();

            if ($request->expectsJson() || str_starts_with(
                    $request->url(),
                    $request->getSchemeAndHttpHost() . '/' . env('APP_API_PREFIX', 'api')
                ) !== false) {
                return response()->json([
                    'success' => false,
                    'message' => "Error",
                    'type' => !empty($e->getCode()) ? $e->getCode() : $this->getTypeByStatus($statusCode),
                    'error' => $msg
                ], $statusCode);
            }

            return response()->view('error', ['code' => $statusCode, 'message' => $msg]);
        }

        return parent::render($request, $e);
    }


    private function getTypeByStatus(int $status): string
    {
        return match ($status) {
            401 => BaseResponseTypeConstant::RESPONSE_TYPE_ERROR_UNAUTHENTICATED,
            403 => BaseResponseTypeConstant::RESPONSE_TYPE_ERROR_FORBIDDEN,
            404 => BaseResponseTypeConstant::RESPONSE_TYPE_ERROR_NOT_FOUND,
            default => BaseResponseTypeConstant::RESPONSE_TYPE_ERROR_OTHER,
        };
    }
}
