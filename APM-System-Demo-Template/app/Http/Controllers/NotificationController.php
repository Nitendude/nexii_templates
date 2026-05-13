<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    public function feed(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->limit(10)
            ->get()
            ->map(function (DatabaseNotification $notification) {
                $data = $notification->data ?? [];
                $timestamp = $data['timestamp'] ?? null;
                if (!$timestamp && $notification->created_at) {
                    $timestamp = Carbon::parse($notification->created_at)->format('Y-m-d H:i:s');
                }

                return [
                    'id' => $notification->id,
                    'is_unread' => is_null($notification->read_at),
                    'title' => $data['title'] ?? 'Update',
                    'message' => $data['message'] ?? '',
                    'timestamp' => $timestamp,
                    'url' => $data['url'] ?? null,
                ];
            })
            ->values();

        return response()->json([
            'unread_count' => $request->user()->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    public function markRead(Request $request, DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== $request->user()->id) {
            abort(403);
        }

        $notification->markAsRead();

        return back();
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
