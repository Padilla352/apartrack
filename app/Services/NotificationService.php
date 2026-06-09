<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification to owner.
     */
    public function sendToOwner($ownerId, $type, $title, $message, $data = [])
    {
        try {
            return Notification::create([
                'owner_id' => $ownerId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'is_read' => false
            ]);
        } catch (\Exception $e) {
            Log::error('Notification send to owner failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send notification to user (tenant).
     */
    public function sendToUser($userId, $type, $title, $message, $data = [])
    {
        try {
            return Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'is_read' => false
            ]);
        } catch (\Exception $e) {
            Log::error('Notification send to user failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get unread count for owner.
     */
    public function getUnreadCountForOwner($ownerId)
    {
        return Notification::where('owner_id', $ownerId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get unread count for user.
     */
    public function getUnreadCountForUser($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get all notifications for owner.
     */
    public function getForOwner($ownerId, $limit = 20)
    {
        return Notification::where('owner_id', $ownerId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all notifications for user.
     */
    public function getForUser($userId, $limit = 20)
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark all notifications as read for owner.
     */
    public function markAllAsReadForOwner($ownerId)
    {
        return Notification::where('owner_id', $ownerId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Mark all notifications as read for user.
     */
    public function markAllAsReadForUser($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }
}