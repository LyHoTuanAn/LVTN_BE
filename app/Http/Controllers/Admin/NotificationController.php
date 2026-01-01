<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Send notification to a single user by ID.
     */
    public function sendToUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'data' => 'nullable|array',
            'image_url' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'VALIDATION_ERROR',
                $validator->errors()->toArray(),
                __('errors.VALIDATION_ERROR'),
                422
            );
        }

        $result = $this->notificationService->sendToUser(
            $request->user_id,
            $request->title,
            $request->body,
            $request->data ?? [],
            $request->image_url
        );

        if (!$result['success']) {
            return $this->errorResponse(
                'NOTIFICATION_SEND_FAILED',
                ['error' => $result['message']],
                __('errors.NOTIFICATION_SEND_FAILED'),
                400
            );
        }

        return $this->successResponse(
            'NOTIFICATION_SENT_SUCCESS',
            $result,
            __('success.NOTIFICATION_SENT_SUCCESS')
        );
    }

    /**
     * Send notification to multiple users by IDs.
     */
    public function sendToUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'data' => 'nullable|array',
            'image_url' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'VALIDATION_ERROR',
                $validator->errors()->toArray(),
                __('errors.VALIDATION_ERROR'),
                422
            );
        }

        $result = $this->notificationService->sendToUsers(
            $request->user_ids,
            $request->title,
            $request->body,
            $request->data ?? [],
            $request->image_url
        );

        if (!$result['success']) {
            return $this->errorResponse(
                'NOTIFICATION_SEND_FAILED',
                ['error' => $result['message']],
                __('errors.NOTIFICATION_SEND_FAILED'),
                400
            );
        }

        return $this->successResponse(
            'NOTIFICATION_SENT_SUCCESS',
            $result,
            __('success.NOTIFICATION_SENT_SUCCESS')
        );
    }

    /**
     * Broadcast notification to all users via Topic Messaging.
     * This is the most efficient way to send notifications to all users.
     */
    public function sendToAllUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'data' => 'nullable|array',
            'image_url' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'VALIDATION_ERROR',
                $validator->errors()->toArray(),
                __('errors.VALIDATION_ERROR'),
                422
            );
        }

        $result = $this->notificationService->sendToAllUsers(
            $request->title,
            $request->body,
            $request->data ?? [],
            $request->image_url
        );

        if (!$result['success']) {
            return $this->errorResponse(
                'NOTIFICATION_SEND_FAILED',
                ['error' => $result['message'] ?? 'Unknown error'],
                __('errors.NOTIFICATION_SEND_FAILED'),
                400
            );
        }

        return $this->successResponse(
            'NOTIFICATION_BROADCAST_SUCCESS',
            $result,
            __('success.NOTIFICATION_BROADCAST_SUCCESS')
        );
    }

    /**
     * Send notification to a specific topic.
     */
    public function sendToTopic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'data' => 'nullable|array',
            'image_url' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'VALIDATION_ERROR',
                $validator->errors()->toArray(),
                __('errors.VALIDATION_ERROR'),
                422
            );
        }

        $result = $this->notificationService->sendToTopic(
            $request->topic,
            $request->title,
            $request->body,
            $request->data ?? [],
            $request->image_url
        );

        if (!$result['success']) {
            return $this->errorResponse(
                'NOTIFICATION_SEND_FAILED',
                ['error' => $result['message'] ?? 'Unknown error'],
                __('errors.NOTIFICATION_SEND_FAILED'),
                400
            );
        }

        return $this->successResponse(
            'NOTIFICATION_SENT_SUCCESS',
            $result,
            __('success.NOTIFICATION_SENT_SUCCESS')
        );
    }

    /**
     * Get available FCM topics.
     */
    public function getTopics()
    {
        $topics = config('firebase.topics', []);

        return $this->successResponse(
            'TOPICS_FETCHED_SUCCESS',
            [
                'topics' => $topics,
                'descriptions' => [
                    'all_users' => 'Send to all app users',
                    'new_movies' => 'New movie announcements',
                    'promotions' => 'Promotions and discounts',
                    'booking_reminders' => 'Booking reminder notifications',
                ],
            ]
        );
    }
}
