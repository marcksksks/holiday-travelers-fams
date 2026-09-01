<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = AppNotification::where('recipient_email', $request->user()->email)
            ->orderByDesc('created_at')->limit(30)->get();

        return response()->json($notifications);
    }

    public function markRead(Request $request, AppNotification $notification)
    {
        abort_unless($notification->recipient_email === $request->user()->email, 403);
        $notification->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    public function markAllRead(Request $request)
    {
        AppNotification::where('recipient_email', $request->user()->email)->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }
}
