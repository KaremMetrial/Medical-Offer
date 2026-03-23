<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NotificationResource;
use App\Http\Resources\PaginationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends BaseController
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(15);
        
        return $this->successResponse([
            'notification_list' =>  NotificationResource::collection($notifications),
            'pagination' => new PaginationResource($notifications),
            'unread_count' => $user->unreadNotifications()->count()
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        
        $notification->markAsRead();
        
        return $this->successResponse(null, __('message.notification_marked_as_read'));
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return $this->successResponse(null, __('message.all_notifications_marked_as_read'));
    }

    /**
     * Remove a specific notification.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        
        $notification->delete();
        
        return $this->successResponse(null, __('message.notification_deleted_successfully'));
    }
}
