<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use stdClass;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user (JSON)
     * Used by the navbar to load initial notifications.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if (!$user) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        // Fallback to dummy data if the notifications table doesn't exist
        if (!Schema::hasTable('notifications')) {
            $dummy = $this->getDummyNotifications();
            return response()->json([
                'notifications' => $dummy,
                'unread_count' => $dummy->whereNull('read_at')->count()
            ]);
        }

        $notifications = $user->notifications()->latest()->get();
        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Get only the unread count (for the badge).
     * Called periodically or after real‑time events.
     *
     * @return JsonResponse
     */
    public function getUnreadCount(): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if (!$user) {
            return response()->json(['count' => 0]);
        }

        if (!Schema::hasTable('notifications')) {
            $dummy = $this->getDummyNotifications();
            return response()->json(['count' => $dummy->whereNull('read_at')->count()]);
        }

        return response()->json(['count' => $user->unreadNotifications()->count()]);
    }

    /**
     * Mark a single notification as read (AJAX POST request).
     *
     * @param string $id
     * @return JsonResponse
     */
    public function markAsRead(string $id): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if (!$user || !Schema::hasTable('notifications')) {
            return response()->json(['success' => false]);
        }

        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read for the authenticated user.
     *
     * @return JsonResponse
     */
    public function markAllAsRead(): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if ($user && Schema::hasTable('notifications')) {
            $user->unreadNotifications->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Delete a single notification (optional).
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if (!$user || !Schema::hasTable('notifications')) {
            return response()->json(['success' => false]);
        }

        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Dummy notifications for testing when the 'notifications' table doesn't exist.
     * Returns a collection of objects mimicking real notification structure.
     *
     * @return Collection<int, stdClass>
     */
    private function getDummyNotifications(): Collection
    {
        return collect([
            (object) [
                'id' => 1,
                'data' => json_encode([
                    'title' => 'New message',
                    'message' => 'You have a new message from Juan',
                    'icon' => '💬'
                ]),
                'read_at' => null,
                'created_at' => now()->subMinutes(10),
            ],
            (object) [
                'id' => 2,
                'data' => json_encode([
                    'title' => 'Apartment available',
                    'message' => 'New listing in Poblacion',
                    'icon' => '🏠'
                ]),
                'read_at' => null,
                'created_at' => now()->subHours(2),
            ],
        ]);
    }
}