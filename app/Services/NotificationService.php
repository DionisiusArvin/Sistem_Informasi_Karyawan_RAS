<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    /**
     * Kirim notifikasi ke user
     *
     * @param int $userId
     * @param string $title
     * @param string $message
     * @param string|null $url
     * @return \App\Models\Notification
     */
    public static function send($userId, $title, $message, $url = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'url'     => $url,
            'is_read' => false,
        ]);
    }
}