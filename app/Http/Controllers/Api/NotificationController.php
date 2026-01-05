<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\Notification\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get list of notifications for the authenticated user.
     * Also returns unread count and marks all as read automatically.
     *
     * @OA\Get(
     *     path="/api/notifications",
     *     summary="Get user notifications",
     *     description="Returns paginated notifications with unread count. All notifications are automatically marked as read when this endpoint is called.",
     *     tags={"Notifications"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="type", in="query", description="Filter by notification type", @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page", in="query", description="Number of items per page", @OA\Schema(type="integer", default=15)),
     *     @OA\Response(response=200, description="Notifications fetched successfully")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $perPage = (int) $request->input('per_page', 15);

        $filters = $request->only(['type']);

        // Get unread count BEFORE marking as read
        $unreadCount = $this->notificationService->getUnreadCount($userId);

        // Get notifications
        $notifications = $this->notificationService->getUserNotifications(
            $userId,
            $filters,
            $perPage
        );

        // Auto mark all as read after fetching
        $this->notificationService->markAllAsRead($userId);

        // Build response data with unread_count included
        $responseData = [
            'unread_count' => $unreadCount,
            'notifications' => NotificationResource::collection($notifications),
        ];

        return $this->successResponse(
            'NOTIFICATIONS_FETCHED_SUCCESS',
            $responseData,
            __('success.NOTIFICATIONS_FETCHED_SUCCESS')
        );
    }
}
