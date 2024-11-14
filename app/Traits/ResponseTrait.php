<?php

namespace App\Traits;

trait ResponseTrait
{

    /**
     * Trả về response thành công
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function responseJsonSuccess($data = null, string $message = 'Operation successful',$meta=null, int $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'errors' => null,
        ], $statusCode);

    }

    /**
     * Trả về response lỗi
     *
     * @param string $message
     * @param int $statusCode
     * @param mixed $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public static function responseJsonError(string $message = 'Operation failed', int $statusCode = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => null,
            'errors' => $errors,
        ], $statusCode);
    }
}

