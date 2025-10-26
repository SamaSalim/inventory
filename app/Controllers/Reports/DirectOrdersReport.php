<?php

namespace App\Controllers\Reports;

use App\Controllers\BaseController;
use App\Models\ItemOrderModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;

class DirectOrdersReport extends BaseController
{
    private ItemOrderModel $model;

    public function __construct()
    {
        $this->model = new ItemOrderModel();
    }

    public function index()
    {
        // Get user role
        $userRole = session()->get('role');
        $userId = session()->get('employee_id') ?? session()->get('user_id');

        // Get filter parameters
        $assetNumber = $this->request->getGet('asset_number');
        $itemName = $this->request->getGet('item_name');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        $recipientId = $this->request->getGet('returned_by'); 

        if (!in_array($userRole, ['assets', 'super_assets'])) {
            $recipientId = $userId;
        }

        $builder = $this->model
            ->select('
                item_order.item_order_id,
                item_order.asset_num,
                item_order.serial_num,
                item_order.note,
                item_order.created_at,
                item_order.created_by,
                item_order.order_id,
                items.name as item_name,
                minor_category.name as minor_category,
                major_category.name as major_category,
                item_order.assets_type,
                item_order.brand,
                item_order.model_num,
                order.from_user_id as sender_id,
                order.to_user_id as recipient_id,
                employee_sender.name as sender_employee_name,
                employee_sender.emp_id as sender_emp_id,
                users_sender.name as sender_user_name,
                users_sender.user_id as sender_user_id,
                employee_recipient.name as recipient_employee_name,
                employee_recipient.emp_id as recipient_emp_id,
                users_recipient.name as recipient_user_name,
                users_recipient.user_id as recipient_user_id,
                order_status.status as order_status_name,
                order_status.id as order_status_id
            ')
            ->join('items', 'items.id = item_order.item_id')
            ->join('minor_category', 'minor_category.id = items.minor_category_id')
            ->join('major_category', 'major_category.id = minor_category.major_category_id')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id')
            ->join('order', 'order.order_id = item_order.order_id', 'left')
            ->join('order_status', 'order_status.id = order.order_status_id', 'left')
            // Join for sender (from_user_id)
            ->join('employee as employee_sender', 'employee_sender.emp_id = order.from_user_id', 'left')
            ->join('users as users_sender', 'users_sender.user_id = order.from_user_id', 'left')
            // Join for recipient (to_user_id)
            ->join('employee as employee_recipient', 'employee_recipient.emp_id = order.to_user_id', 'left')
            ->join('users as users_recipient', 'users_recipient.user_id = order.to_user_id', 'left')
            ->where('usage_status.usage_status', 'جديد')
            ->where('order_status.id', 2); // Only accepted items (status_id = 2)

        // Apply filters
        if (!empty($assetNumber)) {
            $builder->like('item_order.asset_num', $assetNumber);
        }

        if (!empty($itemName)) {
            $builder->like('items.name', $itemName);
        }

        if (!empty($dateFrom)) {
            $builder->where('DATE(item_order.created_at) >=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $builder->where('DATE(item_order.created_at) <=', $dateTo);
        }

        if (!empty($recipientId)) {
            $builder->groupStart()
                ->like('order.to_user_id', $recipientId)
                ->orLike('employee_recipient.emp_id', $recipientId)
                ->orLike('users_recipient.user_id', $recipientId)
                ->groupEnd();
        }

        $directItems = $builder->orderBy('item_order.created_at', 'DESC')->findAll();

        $processedItems = [];
        foreach ($directItems as $item) {
            // Get sender name
            $senderName = $item->sender_employee_name ?? $item->sender_user_name ?? 'غير محدد';
            
            // Get recipient name
            $recipientName = $item->recipient_employee_name ?? $item->recipient_user_name ?? 'غير محدد';
            
            $processedItems[] = [
                'order_id' => $item->order_id,
                'asset_num' => $item->asset_num,
                'serial_num' => $item->serial_num,
                'item_name' => $item->item_name,
                'minor_category' => $item->minor_category,
                'major_category' => $item->major_category,
                'asset_type' => $item->assets_type,
                'brand' => $item->brand ?? '-',
                'model' => $item->model_num ?? '-',
                'created_at' => $item->created_at,
                'notes' => $item->note ?? '',
                'sender_id' => $item->sender_id,
                'sender_name' => $senderName,
                'recipient_id' => $item->recipient_id,
                'recipient_name' => $recipientName,
                'order_status' => $item->order_status_name ?? 'غير محدد'
            ];
        }

        // Get sender and recipient names
        $senderName = 'غير معروف';
        $senderId = 'غير محدد';
        $recipientName = 'غير معروف';
        $recipientId = 'غير محدد';
        
        if (!empty($processedItems)) {
            $senderName = $processedItems[0]['sender_name'];
            $senderId = $processedItems[0]['sender_id'];
            $recipientName = $processedItems[0]['recipient_name'];
            $recipientId = $processedItems[0]['recipient_id'];
        }

        $data = [
            'items' => $processedItems,
            'sender_name' => $senderName,
            'sender_id' => $senderId,
            'recipient_name' => $recipientName,
            'recipient_id' => $recipientId,
            'total_count' => count($processedItems),
            'current_date' => date('Y-m-d'),
            'filters' => [
                'asset_number' => $assetNumber,
                'item_name' => $itemName,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'returned_by' => $recipientId
            ]
        ];

        return view('assets/reports/direct_report_view', $data);
    }

    private function getRecipientName($recipientId = null)
    {
        if (empty($recipientId)) {
            $recipientId = session()->get('employee_id') ?? session()->get('user_id');
        }
        
        if (empty($recipientId)) {
            return 'غير معروف';
        }

        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->where('emp_id', $recipientId)->first();
        
        if ($employee) {
            return $employee->name ?? $employee['name'];
        }

        $userModel = new UserModel();
        $user = $userModel->where('user_id', $recipientId)->first();
        
        if ($user) {
            return $user->name ?? $user['name'];
        }

        return 'غير معروف';
    }
}