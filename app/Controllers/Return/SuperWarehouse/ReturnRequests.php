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

        // First, get the latest evaluation for each item_order_id using a subquery
        $db = \Config\Database::connect();
        $subquery = $db->table('evaluation e1')
            ->select('e1.item_order_id, e1.attachment, e1.created_at')
            ->join('(SELECT item_order_id, MAX(created_at) as max_created 
                     FROM evaluation 
                     GROUP BY item_order_id) e2', 
                    'e1.item_order_id = e2.item_order_id AND e1.created_at = e2.max_created', 
                    'inner')
            ->getCompiledSelect();

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
                      item_order.attachment as item_attachment,
                      latest_eval.attachment as eval_attachment,
                      COALESCE(latest_eval.attachment, item_order.attachment) as attachment,
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
            ->join("({$subquery}) as latest_eval", 'latest_eval.item_order_id = item_order.item_order_id', 'left')
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
        log_message('info', "=== Serving Attachment for Asset: {$assetNum} ===");
        
        // First, try to get the latest attachment from evaluation table
        $db = \Config\Database::connect();
        $builder = $db->table('evaluation');
        $builder->select('evaluation.attachment, evaluation.created_at')
                ->join('item_order', 'item_order.item_order_id = evaluation.item_order_id')
                ->where('item_order.asset_num', $assetNum)
                ->where('evaluation.attachment IS NOT NULL')
                ->where('evaluation.attachment !=', '')
                ->orderBy('evaluation.created_at', 'DESC')
                ->limit(1);
        
        $evaluationResult = $builder->get()->getRow();
        
        $attachment = null;
        $source = null;
        
        if ($evaluationResult && !empty($evaluationResult->attachment)) {
            $attachment = $evaluationResult->attachment;
            $source = 'evaluation';
            log_message('info', "Attachment found in evaluation table: {$attachment}");
            log_message('info', "Evaluation created at: {$evaluationResult->created_at}");
        } else {
            // If not found in evaluation, get from item_order
            $item = $this->itemOrderModel
                ->select('attachment')
                ->where('asset_num', $assetNum)
                ->asArray()
                ->first();
            
            if ($item && !empty($item['attachment'])) {
                $attachment = $item['attachment'];
                $source = 'item_order';
                log_message('info', "Attachment found in item_order table: {$attachment}");
            }
        }

        if (!$attachment || $attachment === 'NULL' || trim($attachment) === '') {
            log_message('error', "No valid attachment found for asset: {$assetNum}");
            return $this->response->setStatusCode(404)->setBody('❌ لا يوجد مرفق لهذا الأصل');
        }

        // Handle comma-separated filenames and get the latest one
        $filenames = array_map('trim', explode(',', $attachment));
        $latestFile = end($filenames);
        
        log_message('info', "All filenames: " . implode(', ', $filenames));
        log_message('info', "Latest file to serve: {$latestFile}");
        
        // Check possible paths
        $possiblePaths = [
            WRITEPATH . 'uploads/evaluation_attachments/' . $latestFile,
            WRITEPATH . 'uploads/return_attachments/' . $latestFile,
            WRITEPATH . 'uploads/attachments/' . $latestFile,
            WRITEPATH . 'uploads/' . $latestFile
        ];
        
        $filePath = null;
        foreach ($possiblePaths as $path) {
            log_message('debug', "Checking path: {$path}");
            if (is_file($path)) {
                $filePath = $path;
                log_message('info', "✓ File found at: {$path}");
                break;
            }
        }

        if (!$filePath) {
            log_message('error', "File not found in any path. Searched for: {$latestFile}");
            log_message('error', "Searched paths: " . implode(', ', $possiblePaths));
            return $this->response->setStatusCode(404)->setBody('❌ الملف غير موجود على السيرفر');
        }

        $extension = strtolower(pathinfo($latestFile, PATHINFO_EXTENSION));
        log_message('info', "File extension: {$extension}");

        if ($extension === 'html' || $extension === 'htm') {
            log_message('info', "Serving HTML file");
            return $this->response
                ->setHeader('Content-Type', 'text/html; charset=UTF-8')
                ->setBody(file_get_contents($filePath));
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        
        log_message('info', "File MIME type: {$mimeType}");
        log_message('info', "=== End Serving Attachment ===");

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($latestFile) . '"')
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
        
        return redirect()->back()->with('success', 'تم قبول الإرجاع بنجاح');
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
        
        return redirect()->back()->with('warning', 'تم رفض الإرجاع');
    }
}