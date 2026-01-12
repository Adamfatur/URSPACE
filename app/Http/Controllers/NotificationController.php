<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        // Handle redirection logic based on notification type
        $link = '#';
        if ($notification->notifiable_type === 'App\Models\Thread') {
            $link = route('threads.show', $notification->notifiable->uuid ?? '');
        } elseif ($notification->notifiable_type === 'App\Models\User') {
            $link = route('profile.show', $notification->notifiable->username ?? '');
        }

        // If the notifiable object is deleted/null, just stay on page
        if (!$notification->notifiable && $notification->type !== 'announcement') {
            return back();
        }

        return redirect($link);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        auth()->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
