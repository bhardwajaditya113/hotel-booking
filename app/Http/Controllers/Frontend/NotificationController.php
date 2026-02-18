<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display listing of notifications
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($request->filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate(20);
        $unreadCount = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return view('frontend.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Get unread count for navbar badge
     */
    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications for dropdown
     */
    public function recent()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $unreadCount = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Show notification settings
     */
    public function settings()
    {
        $user = Auth::user();
        
        // Get user notification preferences
        $preferences = $user->notification_preferences ?? [
            'booking_confirmations' => true,
            'booking_reminders' => true,
            'payment_notifications' => true,
            'promotional_offers' => false,
            'loyalty_updates' => true,
            'review_reminders' => true,
            'email_notifications' => true,
            'sms_notifications' => false,
            'push_notifications' => true,
        ];

        return view('frontend.notifications.settings', compact('preferences'));
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $preferences = [
            'booking_confirmations' => $request->boolean('booking_confirmations'),
            'booking_reminders' => $request->boolean('booking_reminders'),
            'payment_notifications' => $request->boolean('payment_notifications'),
            'promotional_offers' => $request->boolean('promotional_offers'),
            'loyalty_updates' => $request->boolean('loyalty_updates'),
            'review_reminders' => $request->boolean('review_reminders'),
            'email_notifications' => $request->boolean('email_notifications'),
            'sms_notifications' => $request->boolean('sms_notifications'),
            'push_notifications' => $request->boolean('push_notifications'),
        ];

        $user->notification_preferences = $preferences;
        $user->save();

        return back()->with('success', 'Notification preferences updated successfully');
    }

    /**
     * Send notification to user (static helper)
     */
    public static function send($userId, $type, $title, $message, $data = [])
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $data['action_url'] ?? null,
            'action_text' => $data['action_text'] ?? null,
            'channel' => 'database',
        ]);
    }
}
