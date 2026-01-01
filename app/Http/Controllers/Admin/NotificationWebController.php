<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;

class NotificationWebController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display the notification management page.
     */
    public function index()
    {
        $users = User::with('role')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role_id']);
        
        $topics = config('firebase.topics', []);

        return view('admin.notifications.index', compact('users', 'topics'));
    }

    /**
     * Send notification to a single user.
     */
    public function sendToUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'image_url' => 'nullable|url|max:500',
        ]);

        $result = $this->notificationService->sendToUser(
            $validated['user_id'],
            $validated['title'],
            $validated['body'],
            [],
            $validated['image_url'] ?? null
        );

        if ($result['success']) {
            return redirect()->route('admin.notifications.index')
                ->with('success', "Đã gửi thông báo thành công! ({$result['sent_count']} thiết bị)");
        }

        return redirect()->route('admin.notifications.index')
            ->with('error', 'Gửi thông báo thất bại: ' . $result['message']);
    }

    /**
     * Send notification to all users via topic.
     */
    public function sendToAll(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'image_url' => 'nullable|url|max:500',
        ]);

        $result = $this->notificationService->sendToAllUsers(
            $validated['title'],
            $validated['body'],
            [],
            $validated['image_url'] ?? null
        );

        if ($result['success']) {
            return redirect()->route('admin.notifications.index')
                ->with('success', 'Đã gửi thông báo đến tất cả người dùng thành công!');
        }

        return redirect()->route('admin.notifications.index')
            ->with('error', 'Gửi thông báo thất bại: ' . ($result['error'] ?? 'Unknown error'));
    }

    /**
     * Send notification to a specific topic.
     */
    public function sendToTopic(Request $request)
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'image_url' => 'nullable|url|max:500',
        ]);

        $result = $this->notificationService->sendToTopic(
            $validated['topic'],
            $validated['title'],
            $validated['body'],
            [],
            $validated['image_url'] ?? null
        );

        if ($result['success']) {
            return redirect()->route('admin.notifications.index')
                ->with('success', "Đã gửi thông báo đến topic '{$validated['topic']}' thành công!");
        }

        return redirect()->route('admin.notifications.index')
            ->with('error', 'Gửi thông báo thất bại: ' . ($result['error'] ?? 'Unknown error'));
    }
}
