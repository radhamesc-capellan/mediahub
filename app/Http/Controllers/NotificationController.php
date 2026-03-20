<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        try {
            $notifications = auth()->user()
                ->notifications()
                ->latest()
                ->take(20)
                ->get();

            return response()->json($notifications);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function unread()
    {
        try {
            $count = auth()->user()
                ->unreadNotifications()
                ->count();

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }

    public function markAsRead($id)
    {
        try {
            $notification = auth()->user()
                ->notifications()
                ->findOrFail($id);

            $notification->markAsRead();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    public function markAllAsRead()
    {
        try {
            auth()->user()
                ->unreadNotifications
                ->markAsRead();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }
}
