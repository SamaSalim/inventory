<?php

namespace App\Controllers\Reports;

use App\Controllers\BaseController;
use App\Models\ItemOrderModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;

class ReturnReport extends BaseController
{
    private ItemOrderModel $model;

    public function __construct()
    {
        $this->model = new ItemOrderModel();
    }

    public function index()
    {
        // Get user role and info
        $userRole = session()->get('role');
        $userId = session()->get('employee_id') ?? session()->get('user_id');
        $currentUserName = session()->get('name') ?? 'غير معروف';

        // Get super_warehouse user info
        $superWarehouseInfo = $this->getSuperWarehouseUser();

        // Get filter parameters
        $assetNumber = $this->request->getGet('asset_number');
        $itemName = $this->request->getGet('item_name');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        $returnedBy = $this->request->getGet('returned_by');

        // If user is not assets or super_assets, force filter to their own ID
        if (!in_array($userRole, ['assets', 'super_assets'])) {
            $returnedBy = $userId;
        }

        // Build query for returned items with ACCEPTED status only (usage_status_id = 4)
        $builder = $this->model
            ->select('
                item_order.item_order_id,
                item_order.asset_num,
                item_order.note,
                item_order.attachment,
                item_order.updated_at as return_date,
                item_order.created_by,
                item_order.order_id,
                item_order.usage_status_id,
                items.name as item_name,
                minor_category.name as category,
                item_order.assets_type,
                item_order.brand,
                item_order.model_num,
                employee.name as employee_name,
                employee.emp_id,
                users.name as user_name,
                users.user_id,
                order_status.status as order_status_name,
                order_status.id as order_status_id
            ')
            ->join('items', 'items.id = item_order.item_id')
            ->join('minor_category', 'minor_category.id = items.minor_category_id')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id')
            ->join('order', 'order.order_id = item_order.order_id', 'left')
            ->join('order_status', 'order_status.id = order.order_status_id', 'left')
            ->join('employee', 'employee.emp_id = item_order.created_by', 'left')
            ->join('users', 'users.user_id = item_order.created_by', 'left')
            ->where('item_order.usage_status_id', 4); // Only accepted returns (معاد صرفه)

        // Apply filters
        if (!empty($assetNumber)) {
            $builder->like('item_order.asset_num', $assetNumber);
        }

        if (!empty($itemName)) {
            $builder->like('items.name', $itemName);
        }

        if (!empty($dateFrom)) {
            $builder->where('DATE(item_order.updated_at) >=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $builder->where('DATE(item_order.updated_at) <=', $dateTo);
        }

        // Filter by returned_by (employee ID or user ID)
        if (!empty($returnedBy)) {
            $builder->groupStart()
                ->like('employee.emp_id', $returnedBy)
                ->orLike('users.user_id', $returnedBy)
                ->orLike('item_order.created_by', $returnedBy)
                ->groupEnd();
        }

        $returnedItems = $builder->orderBy('item_order.updated_at', 'DESC')->findAll();

        // Check if there are no accepted returned items
        if (empty($returnedItems)) {
            // Return view with no_accepted_items flag
            return view('assets/reports/return_report_view', [
                'items' => [],
                'no_accepted_items' => true,
                'creator_name' => '',
                'creator_id' => '',
                'total_count' => 0,
                'current_date' => date('Y-m-d'),
                'receiver_name' => $superWarehouseInfo['name'],
                'receiver_id' => $superWarehouseInfo['id'],
                'filters' => [
                    'asset_number' => $assetNumber,
                    'item_name' => $itemName,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'returned_by' => $returnedBy
                ]
            ]);
        }

        // Process items to extract reasons from attachments
        $processedItems = [];
        foreach ($returnedItems as $item) {
            $reasons = $this->extractReasonsFromAttachment($item->attachment, $item->asset_num);
            
            $processedItems[] = [
                'asset_num' => $item->asset_num,
                'item_name' => $item->item_name,
                'category' => $item->category,
                'asset_type' => $item->assets_type,
                'brand' => $item->brand ?? '-',
                'model' => $item->model_num ?? '-',
                'return_date' => $item->return_date,
                'notes' => $item->note ?? '',
                'created_by' => $item->created_by,
                'order_status' => $item->order_status_name ?? 'غير محدد',
                'reasons' => $reasons
            ];
        }

        // Get creator name and ID from first item
        $creatorInfo = $this->getCreatorInfo($processedItems[0]['created_by']);

        // Get receiver info (super_warehouse user)
        $receiverName = $superWarehouseInfo['name'];
        $receiverId = $superWarehouseInfo['id'];

        // Prepare data for view
        $data = [
            'items' => $processedItems,
            'no_accepted_items' => false,
            'creator_name' => $creatorInfo['name'],
            'creator_id' => $creatorInfo['id'],
            'total_count' => count($processedItems),
            'current_date' => date('Y-m-d'),
            'receiver_name' => $receiverName,
            'receiver_id' => $receiverId,
            'filters' => [
                'asset_number' => $assetNumber,
                'item_name' => $itemName,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'returned_by' => $returnedBy
            ]
        ];

        return view('assets/reports/return_report_view', $data);
    }

    private function extractReasonsFromAttachment($attachment, $assetNum)
    {
        $reasons = [
            'purpose_end' => false,
            'excess' => false,
            'unfit' => false,
            'damaged' => false
        ];

        if (empty($attachment)) {
            return $reasons;
        }

        $files = explode(',', $attachment);
        $uploadPath = WRITEPATH . 'uploads/return_attachments/';

        foreach ($files as $file) {
            $filename = trim($file);
            
            // Check if it's a generated form file
            if (strpos($filename, 'form_' . $assetNum) === 0) {
                $fullPath = $uploadPath . $filename;
                
                if (file_exists($fullPath)) {
                    $content = file_get_contents($fullPath);
                    
                    // Parse HTML to extract checkmarks
                    $pattern = '/<td class="check-mark">([^<]*)<\/td>/';
                    preg_match_all($pattern, $content, $matches);
                    
                    if (isset($matches[1]) && count($matches[1]) >= 4) {
                        $reasons['purpose_end'] = !empty(trim($matches[1][0]));
                        $reasons['excess'] = !empty(trim($matches[1][1]));
                        $reasons['unfit'] = !empty(trim($matches[1][2]));
                        $reasons['damaged'] = !empty(trim($matches[1][3]));
                    }
                }
                break;
            }
        }

        return $reasons;
    }

    private function getCreatorInfo($createdBy = null)
    {
        if (empty($createdBy)) {
            $createdBy = session()->get('employee_id') ?? session()->get('user_id');
        }
        
        if (empty($createdBy)) {
            return ['name' => 'غير معروف', 'id' => ''];
        }

        // Check if it's an employee
        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->where('emp_id', $createdBy)->first();
        
        if ($employee) {
            return [
                'name' => $employee->name ?? $employee['name'],
                'id' => $employee->emp_id ?? $employee['emp_id']
            ];
        }

        // Check users table
        $userModel = new UserModel();
        $user = $userModel->where('user_id', $createdBy)->first();
        
        if ($user) {
            return [
                'name' => $user->name ?? $user['name'],
                'id' => $user->user_id ?? $user['user_id']
            ];
        }

        return ['name' => 'غير معروف', 'id' => ''];
    }

private function getSuperWarehouseUser()
{

    $employeeModel = new EmployeeModel();

    $result = $employeeModel
        ->select('employee.emp_id, employee.name')
        ->join('permission', 'permission.emp_id = employee.emp_id')
        ->where('permission.role_id', 6) 
        ->asArray()
        ->first();

    if ($result) {
        return [
            'name' => $result['name'],
            'id'   => $result['emp_id']
        ];
    }

    return ['name' => 'غير معروف', 'id' => ''];
}

}