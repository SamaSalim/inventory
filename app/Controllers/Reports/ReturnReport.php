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

        // Process items to extract data from HTML attachments
        $processedItems = [];
        foreach ($returnedItems as $item) {
            $attachmentData = $this->extractDataFromAttachment($item->attachment, $item->asset_num);
            
            // Use data from attachment if available, otherwise use database values
            $processedItems[] = [
                'asset_num' => $item->asset_num,
                'item_name' => $attachmentData['item_name'] ?? $item->item_name,
                'category' => $attachmentData['category'] ?? $item->category,
                'asset_type' => $attachmentData['asset_type'] ?? $item->assets_type,
                'brand' => $item->brand ?? '-',
                'model' => $item->model_num ?? '-',
                'return_date' => $attachmentData['return_date'] ?? $item->return_date,
                'notes' => $attachmentData['notes'] ?? ($item->note ?? ''),
                'created_by' => $item->created_by,
                'order_status' => $item->order_status_name ?? 'غير محدد',
                'reasons' => $attachmentData['reasons'] ?? [
                    'purpose_end' => false,
                    'excess' => false,
                    'unfit' => false,
                    'damaged' => false
                ],
                'returning_entity' => $attachmentData['returning_entity'] ?? 'ادارة العهد',
                'returner_name' => $attachmentData['returner_name'] ?? $this->getCreatorInfo($item->created_by)['name']
            ];
        }

        // Get returner info from first item (person who returned the items)
        // Use returner_name from HTML form if available, otherwise get from database
        $returnerName = $processedItems[0]['returner_name'] ?? $this->getCreatorInfo($processedItems[0]['created_by'])['name'];
        $returnerId = $this->getCreatorInfo($processedItems[0]['created_by'])['id'];

        // Get receiver info (super_warehouse user - person receiving the items)
        $receiverName = $superWarehouseInfo['name'];
        $receiverId = $superWarehouseInfo['id'];

        // Prepare data for view
        $data = [
            'items' => $processedItems,
            'no_accepted_items' => false,
            'creator_name' => $returnerName,  // Person returning items (From)
            'creator_id' => $returnerId,
            'total_count' => count($processedItems),
            'current_date' => date('Y-m-d'),
            'receiver_name' => $receiverName,  // Warehouse keeper receiving items
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

    /**
     * Extract all data from HTML form file (stored separately on disk, not in database)
     */
    private function extractDataFromAttachment($attachment, $assetNum)
    {
        $result = [
            'item_name' => null,
            'category' => null,
            'asset_type' => null,
            'notes' => null,
            'return_date' => null,
            'returning_entity' => null,
            'returner_name' => null,
            'reasons' => [
                'purpose_end' => false,
                'excess' => false,
                'unfit' => false,
                'damaged' => false
            ]
        ];

        // Look for HTML form file by asset number pattern (not in database attachment field)
        $uploadPath = WRITEPATH . 'uploads/return_attachments/';
        $pattern = $uploadPath . 'form_' . $assetNum . '_*.html';
        $files = glob($pattern);
        
        if (empty($files)) {
            // No form found for this asset
            return $result;
        }
        
        // Sort by modification time (newest first)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        // Get the latest form
        $fullPath = $files[0];
        
        if (!file_exists($fullPath)) {
            return $result;
        }
        
        $content = file_get_contents($fullPath);
        
        // Extract return date
        if (preg_match('/<span class="field-value">(\d{4}-\d{2}-\d{2})<\/span>/', $content, $dateMatch)) {
            $result['return_date'] = $dateMatch[1];
        }
        
        // Extract returning entity
        if (preg_match('/<span class="field-label">الجهة المرجعة:<\/span>\s*<span class="field-value">([^<]+)<\/span>/', $content, $entityMatch)) {
            $result['returning_entity'] = trim($entityMatch[1]);
        }
        
        // Extract returner name (from signature section)
        if (preg_match('/الاسم:\s*<span class="signature-value">([^<]+)<\/span>/', $content, $nameMatch)) {
            $result['returner_name'] = trim($nameMatch[1]);
        }
        
        // Extract item data from table rows
        // Look for the specific asset number row
        $assetNumPattern = '/<tr>.*?<td[^>]*>' . preg_quote($assetNum, '/') . '<\/td>.*?<\/tr>/s';
        if (preg_match($assetNumPattern, $content, $rowMatch)) {
            $row = $rowMatch[0];
            
            // Extract item name and category from item-description-cell
            if (preg_match('/<div class="item-main-name">([^<]+)<\/div>\s*<div class="item-sub-details">([^<]+)<\/div>/', $row, $itemMatch)) {
                $result['item_name'] = trim($itemMatch[1]);
                $result['category'] = trim($itemMatch[2]);
            }
            
            // Extract all td elements
            preg_match_all('/<td[^>]*>(.*?)<\/td>/s', $row, $tdMatches);
            
            if (isset($tdMatches[1])) {
                // Asset type is typically in position 3 (after serial, asset_num, description)
                if (isset($tdMatches[1][3])) {
                    $assetType = strip_tags($tdMatches[1][3]);
                    $result['asset_type'] = trim($assetType);
                }
                
                // Extract checkmarks for reasons (positions 6-9)
                if (isset($tdMatches[1][6])) {
                    $result['reasons']['purpose_end'] = !empty(trim(strip_tags($tdMatches[1][6])));
                }
                if (isset($tdMatches[1][7])) {
                    $result['reasons']['excess'] = !empty(trim(strip_tags($tdMatches[1][7])));
                }
                if (isset($tdMatches[1][8])) {
                    $result['reasons']['unfit'] = !empty(trim(strip_tags($tdMatches[1][8])));
                }
                if (isset($tdMatches[1][9])) {
                    $result['reasons']['damaged'] = !empty(trim(strip_tags($tdMatches[1][9])));
                }
                
                // Extract notes (last td with notes-cell class)
                if (isset($tdMatches[1][10])) {
                    $notes = strip_tags($tdMatches[1][10]);
                    $result['notes'] = trim($notes);
                }
            }
        }

        return $result;
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