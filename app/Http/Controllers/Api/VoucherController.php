<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VoucherResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\Voucher\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    use ApiResponseTrait;

    protected VoucherService $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    /**
     * Get list of available vouchers for the authenticated user.
     *
     * @OA\Get(
     *     path="/api/vouchers",
     *     summary="Get available vouchers for user",
     *     description="Returns all vouchers that the authenticated user can use. Optionally filter by movie_id to get vouchers applicable to a specific movie.",
     *     tags={"Vouchers"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="movie_id",
     *         in="query",
     *         description="Filter vouchers applicable to this movie",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Vouchers fetched successfully")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $movieId = $request->input('movie_id') ? (int) $request->input('movie_id') : null;

        $vouchers = $this->voucherService->getAvailableVouchersForUser($userId, $movieId);

        return $this->successResponse(
            'VOUCHERS_FETCHED_SUCCESS',
            VoucherResource::collection($vouchers),
            __('success.VOUCHERS_FETCHED_SUCCESS')
        );
    }
}
