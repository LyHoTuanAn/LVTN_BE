<?php

namespace App\Http\Traits;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
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
    protected function successResponse(
        string $code,
        $data = null,
        ?string $message = null,
        int $statusCode = 200
    ): JsonResponse {
        return ApiResponse::success($code, $data, $message, $statusCode);
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
    protected function errorResponse(
        string $code,
        array $errors = [],
        ?string $message = null,
        int $statusCode = 400
    ): JsonResponse {
        return ApiResponse::error($code, $errors, $message, $statusCode);
    }
}


