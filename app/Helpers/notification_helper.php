<?php
// app/Helpers/notification_helper.php

if (!function_exists('addNotification')) {
    /**
     * إضافة إشعار في الجلسة
     */
    function addNotification($type, $title, $message, $data = [])
    {
        $notifications = session()->get('notifications') ?? [];
        
        $notification = [
            'id' => uniqid(),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'created_at' => date('Y-m-d H:i:s'),
            'is_read' => false
        ];
        
        // إضافة الإشعار في البداية
        array_unshift($notifications, $notification);
        
        // الاحتفاظ بآخر 50 إشعار فقط
        $notifications = array_slice($notifications, 0, 50);
        
        session()->set('notifications', $notifications);
        
        return $notification['id'];
    }
}

if (!function_exists('getNotifications')) {
    function getNotifications($unreadOnly = false)
    {
        $notifications = session()->get('notifications') ?? [];
        
        if ($unreadOnly) {
            return array_filter($notifications, function($n) {
                return !$n['is_read'];
            });
        }
        
        return $notifications;
    }
}

if (!function_exists('markNotificationAsRead')) {
    function markNotificationAsRead($notificationId)
    {
        $notifications = session()->get('notifications') ?? [];
        
        foreach ($notifications as &$notification) {
            if ($notification['id'] === $notificationId) {
                $notification['is_read'] = true;
                break;
            }
        }
        
        session()->set('notifications', $notifications);
    }
}

if (!function_exists('clearNotifications')) {
    function clearNotifications()
    {
        session()->set('notifications', []);
    }
}

if (!function_exists('getUnreadCount')) {
    function getUnreadCount()
    {
        $notifications = getNotifications(true);
        return count($notifications);
    }
}