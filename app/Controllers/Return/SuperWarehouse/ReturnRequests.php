<?php

namespace App\Controllers\Return\SuperWarehouse;

use App\Controllers\BaseController;
use App\Models\ItemOrderModel;
use App\Models\UsageStatusModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;
use App\Models\OrderModel;

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
        // Get filters from request
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

        // Build query - only get items with usage_status_id = 2 (رجيع)
        $builder = $this->itemOrderModel
            ->select('item_order.item_order_id,
                      item_order.order_id,
                      item_order.created_by,
                      item_order.created_at,
                      item_order.usage_status_id,
                      item_order.asset_num,
                      item_order.serial_num,
                      COALESCE(employee.name, users.name) AS employee_name,
                      COALESCE(employee.emp_id, users.user_id) AS emp_id_display,
                      COALESCE(employee.emp_dept, users.user_dept) AS department,
                      usage_status.usage_status,
                      items.name AS item_name')
            ->join('employee', 'employee.emp_id = item_order.created_by', 'left')
            ->join('users', 'users.user_id = item_order.created_by', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->where('item_order.usage_status_id', 2); // Only status "رجيع"

        // Apply general search filter
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

        // Apply specific filters
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

        // Get results as array
        $returnOrders = $builder->orderBy('item_order.created_at', 'DESC')->asArray()->findAll();

        // Get usage statuses for reference
        $usageStatuses = $this->usageStatusModel->asArray()->findAll();

        return view("warehouse/super_warehouse/super_warehouse_view", [
            'returnOrders' => $returnOrders,
            'usageStatuses' => $usageStatuses,
            'filters' => $filters
        ]);
    }

    public function view($itemOrderId)
    {
        // Get detailed information
        $returnItem = $this->itemOrderModel
            ->select('item_order.*,
                      COALESCE(employee.name, users.name) AS returner_name,
                      COALESCE(employee.emp_id, users.user_id) AS returner_id,
                      COALESCE(employee.emp_dept, users.user_dept) AS returner_dept,
                      COALESCE(employee.emp_ext, users.user_ext) AS returner_ext,
                      COALESCE(employee.email, users.email) AS returner_email,
                      usage_status.usage_status,
                      items.name AS item_name,
                      minor_category.name AS minor_category_name,
                      major_category.name AS major_category_name,
                      room.code AS room_code,
                      section.code AS section_code,
                      floor.code AS floor_code,
                      building.code AS building_code')
            ->join('employee', 'employee.emp_id = item_order.created_by', 'left')
            ->join('users', 'users.user_id = item_order.created_by', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->join('room', 'room.id = item_order.room_id', 'left')
            ->join('section', 'section.id = room.section_id', 'left')
            ->join('floor', 'floor.id = section.floor_id', 'left')
            ->join('building', 'building.id = floor.building_id', 'left')
            ->where('item_order.item_order_id', $itemOrderId)
            ->asArray()
            ->first();

        if (!$returnItem) {
            return redirect()->back()->with('error', 'الطلب غير موجود');
        }

        return view('warehouse/super_warehouse/return_details', [
            'returnItem' => $returnItem
        ]);
    }

    public function accept($itemOrderId)
    {
        try {
            // Get the item order
            $itemOrder = $this->itemOrderModel->asArray()->find($itemOrderId);
            
            if (!$itemOrder) {
                return redirect()->back()->with('error', 'الطلب غير موجود');
            }

            // Check if item is in "رجيع" status (2)
            if ($itemOrder['usage_status_id'] != 2) {
                return redirect()->back()->with('error', 'لا يمكن قبول هذا الطلب');
            }

            // Update item_order status to "اعادة صرف" - you need to check what ID this is in your database
            // Assuming "اعادة صرف" has ID 4 (please verify this in your usage_status table)
            $updated = $this->itemOrderModel->update($itemOrderId, [
                'usage_status_id' => 4, // اعادة صرف
                'note' => ($itemOrder['note'] ?? '') . ' | تم قبول الإرجاع في: ' . date('Y-m-d H:i:s')
            ]);

            if ($updated) {
                return redirect()->to('return/superWarehouse/returnrequests')->with('success', 'تم قبول الإرجاع بنجاح وتغيير الحالة إلى اعادة صرف');
            } else {
                return redirect()->back()->with('error', 'حدث خطأ أثناء قبول الإرجاع');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error accepting return: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function reject($itemOrderId)
    {
        try {
            // Get the item order
            $itemOrder = $this->itemOrderModel->asArray()->find($itemOrderId);
            
            if (!$itemOrder) {
                return redirect()->back()->with('error', 'الطلب غير موجود');
            }

            // Check if item is in "رجيع" status (2)
            if ($itemOrder['usage_status_id'] != 2) {
                return redirect()->back()->with('error', 'لا يمكن رفض هذا الطلب');
            }

            // Update item_order status to "مرفوض" - you need to check what ID this is in your database
            // Assuming "مرفوض" has ID 5 (please verify this in your usage_status table)
            $updated = $this->itemOrderModel->update($itemOrderId, [
                'usage_status_id' => 5, // مرفوض
                'note' => ($itemOrder['note'] ?? '') . ' | تم رفض الإرجاع في: ' . date('Y-m-d H:i:s')
            ]);

            if ($updated) {
                return redirect()->to('return/superWarehouse/returnrequests')->with('success', 'تم رفض الإرجاع');
            } else {
                return redirect()->back()->with('error', 'حدث خطأ أثناء رفض الإرجاع');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error rejecting return: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to get returner information
     * Checks both employee and users tables
     */
    private function getReturnerInfo($createdBy)
    {
        // Try to find in employee table first
        $employee = $this->employeeModel->where('emp_id', $createdBy)->asArray()->first();
        
        if ($employee) {
            return [
                'name' => $employee['name'],
                'id' => $employee['emp_id'],
                'dept' => $employee['emp_dept'],
                'ext' => $employee['emp_ext'],
                'email' => $employee['email']
            ];
        }

        // If not found, try users table
        $user = $this->userModel->where('user_id', $createdBy)->asArray()->first();
        
        if ($user) {
            return [
                'name' => $user['name'],
                'id' => $user['user_id'],
                'dept' => $user['user_dept'],
                'ext' => $user['user_ext'],
                'email' => $user['email']
            ];
        }

        return null;
    }
}