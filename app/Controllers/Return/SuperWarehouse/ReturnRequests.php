<?php

namespace App\Controllers\Return\SuperWarehouse;

use App\Controllers\BaseController;
use App\Models\ItemOrderModel;
use App\Models\UsageStatusModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;
use App\Models\OrderModel;
use App\Models\HistoryModel;

class ReturnRequests extends BaseController
{
    protected $itemOrderModel;
    protected $usageStatusModel;
    protected $employeeModel;
    protected $userModel;
    protected $orderModel;

    public function __construct()
    {
        $this->itemOrderModel = new ItemOrderModel();
        $this->usageStatusModel = new UsageStatusModel();
        $this->employeeModel = new EmployeeModel();
        $this->userModel = new UserModel();
        $this->orderModel = new OrderModel();
    }

    public function index(): string
    {
        // DEBUG: Log all session data
        log_message('debug', '=== SESSION DEBUG ===');
        log_message('debug', 'All session data: ' . json_encode(session()->get()));
        log_message('debug', 'emp_id: ' . (session()->get('emp_id') ?? 'NULL'));
        log_message('debug', 'user_id: ' . (session()->get('user_id') ?? 'NULL'));
        log_message('debug', 'name: ' . (session()->get('name') ?? 'NULL'));
        log_message('debug', '===================');

        $filters = [
            'general_search' => $this->request->getGet('general_search'),
            'order_id' => $this->request->getGet('order_id'),
            'emp_id' => $this->request->getGet('emp_id'),
            'employee_name' => $this->request->getGet('employee_name'),
            'item_name' => $this->request->getGet('item_name'),
            'asset_num' => $this->request->getGet('asset_num'),
            'serial_num' => $this->request->getGet('serial_num'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $builder = $this->itemOrderModel
            ->select('item_order.item_order_id,
                      item_order.order_id,
                      item_order.created_by,
                      item_order.created_at,
                      item_order.usage_status_id,
                      item_order.asset_num,
                      item_order.serial_num,
                      item_order.brand,
                      item_order.model_num,
                      item_order.note,
                      item_order.attachment,
                      COALESCE(employee.name, users.name) AS employee_name,
                      COALESCE(employee.emp_id, users.user_id) AS emp_id_display,
                      COALESCE(employee.emp_dept, users.user_dept) AS department,
                      usage_status.usage_status,
                      items.name AS item_name,
                      minor_category.name AS minor_category_name')
            ->join('employee', 'employee.emp_id = item_order.created_by', 'left')
            ->join('users', 'users.user_id = item_order.created_by', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->where('item_order.usage_status_id', 2);

        if (!empty($filters['general_search'])) {
            $searchTerm = $filters['general_search'];
            $builder->groupStart()
                ->like('item_order.item_order_id', $searchTerm)
                ->orLike('item_order.created_by', $searchTerm)
                ->orLike('employee.name', $searchTerm)
                ->orLike('users.name', $searchTerm)
                ->orLike('items.name', $searchTerm)
                ->orLike('item_order.asset_num', $searchTerm)
                ->orLike('item_order.serial_num', $searchTerm)
                ->groupEnd();
        }

        if (!empty($filters['order_id'])) {
            $builder->like('item_order.item_order_id', $filters['order_id']);
        }

        if (!empty($filters['emp_id'])) {
            $builder->groupStart()
                ->like('employee.emp_id', $filters['emp_id'])
                ->orLike('users.user_id', $filters['emp_id'])
                ->orLike('item_order.created_by', $filters['emp_id'])
                ->groupEnd();
        }

        if (!empty($filters['employee_name'])) {
            $builder->groupStart()
                ->like('employee.name', $filters['employee_name'])
                ->orLike('users.name', $filters['employee_name'])
                ->groupEnd();
        }

        if (!empty($filters['item_name'])) {
            $builder->like('items.name', $filters['item_name']);
        }

        if (!empty($filters['asset_num'])) {
            $builder->like('item_order.asset_num', $filters['asset_num']);
        }

        if (!empty($filters['serial_num'])) {
            $builder->like('item_order.serial_num', $filters['serial_num']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('DATE(item_order.created_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('DATE(item_order.created_at) <=', $filters['date_to']);
        }

        $returnOrders = $builder->orderBy('item_order.created_at', 'DESC')->asArray()->findAll();
        $usageStatuses = $this->usageStatusModel->asArray()->findAll();

        return view("warehouse/super_warehouse/super_warehouse_view", [
            'returnOrders' => $returnOrders,
            'usageStatuses' => $usageStatuses,
            'filters' => $filters
        ]);
    }

    public function serveAttachment($assetNum)
    {
        $item = $this->itemOrderModel
            ->select('attachment')
            ->where('asset_num', $assetNum)
            ->first();

        if (!$item || empty($item->attachment)) {
            return $this->response->setStatusCode(404)->setBody('❌ لا يوجد مرفق لهذا الأصل');
        }

        $filenames = explode(',', $item->attachment);
        $latestFile = trim(end($filenames));
        $filePath = WRITEPATH . 'uploads/return_attachments/' . $latestFile;

        if (!is_file($filePath)) {
            return $this->response->setStatusCode(404)->setBody('❌ الملف غير موجود');
        }

        $extension = strtolower(pathinfo($latestFile, PATHINFO_EXTENSION));

        if ($extension === 'html') {
            return $this->response
                ->setHeader('Content-Type', 'text/html; charset=UTF-8')
                ->setBody(file_get_contents($filePath));
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . $latestFile . '"')
            ->setBody(file_get_contents($filePath));
    }

    private function getCurrentUserId()
    {
        // Get all session data for debugging
        $sessionData = session()->get();
        
        // Log everything
        log_message('debug', '=== getCurrentUserId() DEBUG ===');
        log_message('debug', 'Full session: ' . json_encode($sessionData));
        
        // Try different possible session key names
        $possibleKeys = ['emp_id', 'user_id', 'id', 'employee_id', 'userId', 'empId'];
        
        foreach ($possibleKeys as $key) {
            $value = session()->get($key);
            log_message('debug', "Checking key '{$key}': " . ($value ?? 'NULL'));
            
            if (!empty($value)) {
                log_message('info', "Found user ID in session key '{$key}': {$value}");
                return $value;
            }
        }
        
        log_message('error', 'No valid user ID found in any session key');
        log_message('debug', '===============================');
        
        return null;
    }

    public function acceptReturn($itemOrderId)
    {
        // DEBUG: Log session at start
        log_message('debug', '=== ACCEPT RETURN START ===');
        log_message('debug', 'Item Order ID: ' . $itemOrderId);
        log_message('debug', 'Session data: ' . json_encode(session()->get()));
        
        $currentUserId = $this->getCurrentUserId();
        
        log_message('debug', 'Current User ID resolved to: ' . ($currentUserId ?? 'NULL'));
        
        if (!$currentUserId) {
            log_message('error', 'Accept Return: No user ID found in session');
            
            // Return detailed error with session info for debugging
            $sessionInfo = json_encode(session()->get());
            return redirect()->back()->with('error', '❌ فشل في تحديد المستخدم الحالي. بيانات الجلسة: ' . $sessionInfo);
        }

        $updateData = [
            'usage_status_id' => 4,
            'created_by' => $currentUserId
        ];
        
        log_message('debug', 'Update data: ' . json_encode($updateData));
        
        $updateResult = $this->itemOrderModel->update($itemOrderId, $updateData);
        
        if (!$updateResult) {
            $errors = $this->itemOrderModel->errors();
            log_message('error', 'Failed to update item_order: ' . json_encode($errors));
            return redirect()->back()->with('error', '❌ فشل في تحديث حالة الطلب: ' . json_encode($errors));
        }

        log_message('info', 'Item order updated successfully');

        $historyModel = new HistoryModel();
        $historyData = [
            'item_order_id' => $itemOrderId,
            'usage_status_id' => 4,
            'handled_by' => $currentUserId
        ];
        
        log_message('debug', 'History data: ' . json_encode($historyData));
        
        $historyResult = $historyModel->insert($historyData);
        
        if (!$historyResult) {
            $historyErrors = $historyModel->errors();
            log_message('error', 'Failed to insert history: ' . json_encode($historyErrors));
        } else {
            log_message('info', 'History inserted successfully with ID: ' . $historyResult);
        }

        log_message('info', "Return accepted successfully. Item Order ID: {$itemOrderId}, User: {$currentUserId}");
        log_message('debug', '=== ACCEPT RETURN END ===');
        
        return redirect()->back()->with('success', '');
    }

    public function rejectReturn($itemOrderId)
    {
        // DEBUG: Log session at start
        log_message('debug', '=== REJECT RETURN START ===');
        log_message('debug', 'Item Order ID: ' . $itemOrderId);
        log_message('debug', 'Session data: ' . json_encode(session()->get()));
        
        $currentUserId = $this->getCurrentUserId();
        
        log_message('debug', 'Current User ID resolved to: ' . ($currentUserId ?? 'NULL'));
        
        if (!$currentUserId) {
            log_message('error', 'Reject Return: No user ID found in session');
            
            // Return detailed error with session info for debugging
            $sessionInfo = json_encode(session()->get());
            return redirect()->back()->with('error', '❌ فشل في تحديد المستخدم الحالي. بيانات الجلسة: ' . $sessionInfo);
        }

        $updateData = [
            'usage_status_id' => 5,
            'created_by' => $currentUserId
        ];
        
        log_message('debug', 'Update data: ' . json_encode($updateData));
        
        $updateResult = $this->itemOrderModel->update($itemOrderId, $updateData);
        
        if (!$updateResult) {
            $errors = $this->itemOrderModel->errors();
            log_message('error', 'Failed to update item_order: ' . json_encode($errors));
            return redirect()->back()->with('error', '❌ فشل في تحديث حالة الطلب: ' . json_encode($errors));
        }

        log_message('info', 'Item order updated successfully');

        $historyModel = new HistoryModel();
        $historyData = [
            'item_order_id' => $itemOrderId,
            'usage_status_id' => 5,
            'handled_by' => $currentUserId
        ];
        
        log_message('debug', 'History data: ' . json_encode($historyData));
        
        $historyResult = $historyModel->insert($historyData);
        
        if (!$historyResult) {
            $historyErrors = $historyModel->errors();
            log_message('error', 'Failed to insert history: ' . json_encode($historyErrors));
        } else {
            log_message('info', 'History inserted successfully with ID: ' . $historyResult);
        }

        log_message('info', "Return rejected successfully. Item Order ID: {$itemOrderId}, User: {$currentUserId}");
        log_message('debug', '=== REJECT RETURN END ===');
        
        return redirect()->back()->with('warning', ' تم رفض الإرجاع');
    }
}