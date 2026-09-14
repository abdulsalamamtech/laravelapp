<?php

namespace App\Http\Controllers\V1\Company;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all unread notifications for the logged-in user
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->unreadNotifications;

        // not found
        // You should return 200 OK with an empty payload.
        if ($notifications->isEmpty()) {
            return ApiResponse::success([], 'No unread notifications found');
        }

        return ApiResponse::success($notifications, 'successful');
    }

    /**
     * Mark a specific single notification as read
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();

            return ApiResponse::success([], 'notifications marked as read');
        }

        return ApiResponse::success([], 'notifications not found', 404);
    }

    /**
     * Mark all notifications as read at once
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return ApiResponse::success([], 'All notifications marked as read');
    }

    /**
     * Delete all read notifications
     */
    public function deleteAllRead(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->whereNotNull('read_at')
            ->delete();

        if (! $notifications) {
            return ApiResponse::error([], 'unable to delete notifications', 403);
        }

        return ApiResponse::success([], 'notifications deleted successfully');
    }
}
