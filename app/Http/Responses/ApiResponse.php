<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Trả về response thành công
     *
     * @param string $code
     * @param mixed $data
     * @param string|null $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function success(
        string $code,
        $data = null,
        ?string $message = null,
        int $statusCode = 200
    ): JsonResponse {
        $locale = app()->getLocale();
        $message = $message ?? __("success.{$code}");

        return response()->json([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Trả về response lỗi
     *
     * @param string $code
     * @param array $errors
     * @param string|null $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function error(
        string $code,
        array $errors = [],
        ?string $message = null,
        int $statusCode = 400
    ): JsonResponse {
        $message = $message ?? __("errors.{$code}");

        return response()->json([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}


