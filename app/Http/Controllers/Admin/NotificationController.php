<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * عرض جميع الإشعارات
     */
    public function index(Request $request)
    {
        $notifications = auth('super_admin')->user()
            ->notifications()
            ->when($request->type, function($query, $type) {
                return $query->where('type', $type);
            })
            ->when($request->read_status, function($query, $status) {
                if ($status === 'read') {
                    return $query->whereNotNull('read_at');
                } elseif ($status === 'unread') {
                    return $query->whereNull('read_at');
                }
                return $query;
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * تحديد إشعار كمقروء
     */
    public function markAsRead(DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديد الإشعار كمقروء'
        ]);
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        auth('super_admin')->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديد جميع الإشعارات كمقروءة'
        ]);
    }

    /**
     * حذف إشعار
     */
    public function destroy(DatabaseNotification $notification)
    {
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الإشعار'
        ]);
    }

    /**
     * إعدادات الإشعارات
     */
    public function settings()
    {
        $admin = auth('super_admin')->user();
        
        return view('admin.notifications.settings', compact('admin'));
    }

    /**
     * تحديث إعدادات الإشعارات
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'browser_notifications' => 'boolean',
            'tenant_notifications' => 'boolean',
            'security_notifications' => 'boolean',
        ]);

        $admin = auth('super_admin')->user();
        
        // TODO: حفظ إعدادات الإشعارات في جدول منفصل أو في metadata
        
        return redirect()->back()->with('success', 'تم تحديث إعدادات الإشعارات');
    }
}
