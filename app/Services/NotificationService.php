<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public static function send($userId, $title, $message, $url)
    {
        Notification::create([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'url'     => $url,
            'is_read' => false,
        ]);
    }
}
