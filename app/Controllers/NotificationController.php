<?php
// app/Controllers/NotificationController.php

namespace App\Controllers;

class NotificationController extends BaseController
{
    public function __construct()
    {
        helper('notification');
    }
    
    /**
     * جلب إشعارات المستخدم من الجلسة
     */
    public function getUserNotifications()
    {
        $userId = session()->get('user_id');
        $employeeId = session()->get('employee_id');
        
        if (!$userId && !$employeeId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'غير مصرح'
            ])->setStatusCode(401);
        }
        
        $unreadOnly = $this->request->getGet('unread') === 'true';
        $notifications = getNotifications($unreadOnly);
        
        // إضافة الوقت النسبي
        foreach ($notifications as &$notification) {
            $notification['time_ago'] = $this->timeAgo($notification['created_at']);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'notifications' => array_values($notifications)
        ]);
    }
    
    /**
     * جلب الإشعارات الجديدة من قاعدة البيانات
     */
    public function checkNewEvents()
    {
        $userId = session()->get('user_id');
        $employeeId = session()->get('employee_id');
        
        if (!$userId && !$employeeId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'غير مصرح'
            ])->setStatusCode(401);
        }
        
        $userId = session()->get('employee_id');
        $isEmployee = session()->get('isEmployee');
        $lastCheck = session()->get('last_notification_check') ?? date('Y-m-d H:i:s', strtotime('-1 hour'));
        
        $newEvents = [];
        
        // فحص التحويلات الجديدة للمستخدمين
        if (!$isEmployee) {
            $transferModel = new \App\Models\TransferItemsModel();
            
            // التحويلات الواردة الجديدة
            $newTransfers = $transferModel
                ->select('transfer_items.*, from_user.name as from_name')
                ->join('users as from_user', 'from_user.user_id = transfer_items.from_user_id')
                ->where('to_user_id', $userId)
                ->where('transfer_items.created_at >', $lastCheck)
                ->where('order_status_id', 1) // قيد الانتظار
                ->findAll();
                
            foreach ($newTransfers as $transfer) {
                addNotification(
                    'transfer',
                    'طلب تحويل جديد',
                    'لديك طلب تحويل جديد من ' . $transfer->from_name,
                    ['transfer_id' => $transfer->transfer_item_id]
                );
                $newEvents[] = 'transfer';
            }
            
            // التحويلات المقبولة/المرفوضة
            $statusUpdates = $transferModel
                ->where('from_user_id', $userId)
                ->where('updated_at >', $lastCheck)
                ->whereIn('order_status_id', [2, 3])
                ->findAll();
                
            foreach ($statusUpdates as $update) {
                $status = $update->order_status_id == 2 ? 'مقبول' : 'مرفوض';
                addNotification(
                    'order_status',
                    'تحديث حالة التحويل',
                    'تم ' . $status . ' طلب التحويل الخاص بك',
                    ['transfer_id' => $update->transfer_item_id]
                );
                $newEvents[] = 'status';
            }
        }
        
        // فحص العمليات للموظفين
        if ($isEmployee) {
            $orderModel = new \App\Models\OrderModel();
            
            $newOrders = $orderModel
                ->where('to_employee_id', $userId)
                ->where('created_at >', $lastCheck)
                ->findAll();
                
            foreach ($newOrders as $order) {
                addNotification(
                    'new_order',
                    'طلب جديد',
                    'لديك طلب جديد رقم ' . $order->order_id,
                    ['order_id' => $order->order_id]
                );
                $newEvents[] = 'order';
            }
        }
        
        // تحديث آخر وقت فحص
        session()->set('last_notification_check', date('Y-m-d H:i:s'));
        
        return $this->response->setJSON([
            'success' => true,
            'new_events' => count($newEvents),
            'unread_count' => getUnreadCount()
        ]);
    }
    
    /**
     * تحديد إشعار كمقروء
     */
    public function markAsRead($id)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'غير مصرح'
            ]);
        }
        
        markNotificationAsRead($id);
        
        return $this->response->setJSON(['success' => true]);
    }
    
    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'غير مصرح'
            ]);
        }
        
        $notifications = session()->get('notifications') ?? [];
        
        foreach ($notifications as &$notification) {
            $notification['is_read'] = true;
        }
        
        session()->set('notifications', $notifications);
        
        return $this->response->setJSON(['success' => true]);
    }
    
    /**
     * مسح جميع الإشعارات
     */
    public function clearAll()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'غير مصرح'
            ]);
        }
        
        clearNotifications();
        
        return $this->response->setJSON(['success' => true]);
    }
    
    private function timeAgo($datetime)
    {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) {
            return 'الآن';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return "منذ {$minutes} دقيقة";
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return "منذ {$hours} ساعة";
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return "منذ {$days} يوم";
        } else {
            return date('Y-m-d', $time);
        }
    }
}