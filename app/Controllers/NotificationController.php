<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class NotificationController extends Controller
{
    protected $db;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    
    /**
     * التحقق من صلاحية الوصول
     */
    private function checkAccess()
    {
        // للمستخدمين العاديين
        if (session()->has('user_id')) {
            return [
                'allowed' => true,
                'type' => 'user',
                'id' => session()->get('user_id')
            ];
        }
        
        // للموظفين - فقط مدير العهد
        if (session()->has('employee_id')) {
            $employee = $this->db->table('employees')
                ->where('id', session()->get('employee_id'))
                ->get()
                ->getRow();
                
            if ($employee && $employee->department === 'super assets') {
                return [
                    'allowed' => true,
                    'type' => 'super_assets',
                    'id' => $employee->id
                ];
            }
        }
        
        return ['allowed' => false];
    }
    
    /**
     * جلب الإشعارات
     */
    public function getUserNotifications()
    {
        $access = $this->checkAccess();
        
        if (!$access['allowed']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'غير مصرح'
            ])->setStatusCode(403);
        }
        
        $notifications = $this->getNotificationsForUser($access['type'], $access['id']);
        
        return $this->response->setJSON([
            'success' => true,
            'notifications' => $notifications
        ]);
    }
    
    /**
     * جلب الإشعارات حسب نوع المستخدم
     */
    private function getNotificationsForUser($type, $userId)
    {
        $notifications = [];
        
        if ($type === 'user') {
            // إشعارات المستخدم العادي
            $notifications = $this->getUserTransferNotifications($userId);
            
        } elseif ($type === 'super_assets') {
            // إشعارات مدير العهد - كل الحركات
            $notifications = $this->getSuperAssetsNotifications();
        }
        
        return $notifications;
    }
    
    /**
     * إشعارات المستخدم العادي
     */
    private function getUserTransferNotifications($userId)
    {
        $notifications = [];
        
        // جلب التحويلات المرسلة
        $sentTransfers = $this->db->table('orders')
            ->select('orders.*, users.name as to_user_name')
            ->join('users', 'users.id = orders.to_user_id', 'left')
            ->where('orders.from_user_id', $userId)
            ->where('orders.type', 'transfer')
            ->orderBy('orders.created_at', 'DESC')
            ->limit(20)
            ->get()
            ->getResult();
            
        foreach ($sentTransfers as $transfer) {
            $notifications[] = [
                'id' => 'transfer_sent_' . $transfer->id,
                'type' => 'transfer',
                'title' => 'طلب تحويل مرسل',
                'message' => "تم إرسال طلب تحويل إلى {$transfer->to_user_name} - الحالة: {$this->getStatusText($transfer->status)}",
                'time_ago' => $this->timeAgo($transfer->created_at),
                'is_read' => false,
                'data' => ['order_id' => $transfer->id]
            ];
        }
        
        // جلب التحويلات المستلمة
        $receivedTransfers = $this->db->table('orders')
            ->select('orders.*, users.name as from_user_name')
            ->join('users', 'users.id = orders.from_user_id', 'left')
            ->where('orders.to_user_id', $userId)
            ->where('orders.type', 'transfer')
            ->orderBy('orders.created_at', 'DESC')
            ->limit(20)
            ->get()
            ->getResult();
            
        foreach ($receivedTransfers as $transfer) {
            $notifications[] = [
                'id' => 'transfer_received_' . $transfer->id,
                'type' => 'transfer',
                'title' => 'طلب تحويل وارد',
                'message' => "تم استلام طلب تحويل من {$transfer->from_user_name}",
                'time_ago' => $this->timeAgo($transfer->created_at),
                'is_read' => false,
                'data' => ['order_id' => $transfer->id]
            ];
        }
        
        return $notifications;
    }
    
    /**
     * إشعارات مدير العهد - كل حركات النظام
     */
    private function getSuperAssetsNotifications()
    {
        $notifications = [];
        
        // جلب جميع التحويلات بين المستخدمين
        $transfers = $this->db->table('orders')
            ->select('orders.*, 
                     from_user.name as from_user_name, 
                     to_user.name as to_user_name')
            ->join('users as from_user', 'from_user.id = orders.from_user_id', 'left')
            ->join('users as to_user', 'to_user.id = orders.to_user_id', 'left')
            ->where('orders.type', 'transfer')
            ->orderBy('orders.created_at', 'DESC')
            ->limit(50)
            ->get()
            ->getResult();
            
        foreach ($transfers as $transfer) {
            $statusIcon = $this->getStatusIcon($transfer->status);
            $notifications[] = [
                'id' => 'admin_transfer_' . $transfer->id,
                'type' => 'admin_transfer',
                'title' => $statusIcon . ' تحويل عهدة بين مستخدمين',
                'message' => "من: {$transfer->from_user_name} → إلى: {$transfer->to_user_name} | الحالة: {$this->getStatusText($transfer->status)}",
                'time_ago' => $this->timeAgo($transfer->created_at),
                'is_read' => false,
                'data' => [
                    'order_id' => $transfer->id,
                    'from_user' => $transfer->from_user_name,
                    'to_user' => $transfer->to_user_name,
                    'status' => $transfer->status
                ]
            ];
        }
        
        // جلب عمليات الإرجاع
        $returns = $this->db->table('returns')
            ->select('returns.*, users.name as user_name')
            ->join('users', 'users.id = returns.user_id', 'left')
            ->orderBy('returns.created_at', 'DESC')
            ->limit(30)
            ->get()
            ->getResult();
            
        foreach ($returns as $return) {
            $notifications[] = [
                'id' => 'admin_return_' . $return->id,
                'type' => 'admin_return',
                'title' => '↩️ إرجاع عهدة',
                'message' => "المستخدم: {$return->user_name} قام بإرجاع عهدة",
                'time_ago' => $this->timeAgo($return->created_at),
                'is_read' => false,
                'data' => [
                    'return_id' => $return->id,
                    'user_name' => $return->user_name
                ]
            ];
        }
        
        // ترتيب الإشعارات حسب التاريخ
        usort($notifications, function($a, $b) {
            return strtotime($b['time_ago']) - strtotime($a['time_ago']);
        });
        
        return array_slice($notifications, 0, 50);
    }
    
    /**
     * تحويل الوقت إلى صيغة منذ
     */
    private function timeAgo($datetime)
    {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return 'الآن';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return "منذ {$mins} دقيقة";
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return "منذ {$hours} ساعة";
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return "منذ {$days} يوم";
        } else {
            return date('Y-m-d', $timestamp);
        }
    }
    
    /**
     * نص الحالة بالعربي
     */
    private function getStatusText($status)
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
            'completed' => 'مكتمل'
        ];
        
        return $statuses[$status] ?? $status;
    }
    
    /**
     * أيقونة الحالة
     */
    private function getStatusIcon($status)
    {
        $icons = [
            'pending' => '⏳',
            'accepted' => '✅',
            'rejected' => '❌',
            'completed' => '🎉'
        ];
        
        return $icons[$status] ?? '📋';
    }
    
    /**
     * تعليم الكل كمقروء
     */
    public function markAllAsRead()
    {
        $access = $this->checkAccess();
        
        if (!$access['allowed']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'غير مصرح'
            ])->setStatusCode(403);
        }
        
        // حفظ في Session أن كل الإشعارات مقروءة
        session()->set('notifications_last_read_' . $access['id'], time());
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'تم تعليم جميع الإشعارات كمقروءة'
        ]);
    }
    
    /**
     * مسح جميع الإشعارات
     */
    public function clearAll()
    {
        $access = $this->checkAccess();
        
        if (!$access['allowed']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'غير مصرح'
            ])->setStatusCode(403);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'تم مسح جميع الإشعارات'
        ]);
    }
}