<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function notify(User $user, string $title, string $message, string $type = 'info', ?string $actionUrl = null): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'action_url' => $actionUrl,
        ]);
    }

    public function notifyMultiple(array $userIds, string $title, string $message, string $type = 'info'): void
    {
        foreach ($userIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
            ]);
        }
    }

    public function getUserNotifications(User $user, int $limit = 10)
    {
        return $user->notifications()
            ->latest()
            ->paginate($limit);
    }

    public function getUnreadCount(User $user): int
    {
        return $user->notifications()->unread()->count();
    }

    public function markAllAsRead(User $user): void
    {
        $user->notifications()->unread()->update(['is_read' => true]);
    }
}
