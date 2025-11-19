<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ItemOrderModel;
use App\Models\TransferItemsModel;
use App\Models\HistoryModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;
use App\Models\EvaluationModel;
use App\Exceptions\AuthenticationException;
use CodeIgniter\HTTP\ResponseInterface;

class AssetsHistory extends BaseController
{
    private function checkAuth()
    {
        if (!session()->get('isLoggedIn')) {
            throw new AuthenticationException();
        }
    }

    public function superAssets(): string
    {
        if (!session()->get('isLoggedIn')) {
            throw new AuthenticationException();
        }

        $itemOrderModel = new ItemOrderModel();
        $transferItemsModel = new TransferItemsModel();
        $historyModel = new HistoryModel();

        $filters = [
            'search' => $this->request->getGet('search'),
            'asset_number' => $this->request->getGet('asset_number'),
            'item_name' => $this->request->getGet('item_name'),
            'operation_type' => $this->request->getGet('operation_type'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
        ];

        // Get transfers
        $transfersQuery = $transferItemsModel
            ->select('item_order.asset_num as asset_number, items.name as item_name,
                  transfer_items.created_at as last_operation_date, item_order.item_order_id as id,
                  transfer_items.transfer_item_id')
            ->join('item_order', 'item_order.item_order_id = transfer_items.item_order_id', 'left')
            ->join('items', 'items.id = item_order.item_id', 'left');

        if (!empty($filters['asset_number'])) {
            $transfersQuery->like('item_order.asset_num', $filters['asset_number']);
        }
        if (!empty($filters['item_name'])) {
            $transfersQuery->like('items.name', $filters['item_name']);
        }
        if (!empty($filters['date_from'])) {
            $transfersQuery->where('transfer_items.created_at >=', $filters['date_from'] . ' 00:00:00');
        }
        if (!empty($filters['date_to'])) {
            $transfersQuery->where('transfer_items.created_at <=', $filters['date_to'] . ' 23:59:59');
        }
        if (!empty($filters['search'])) {
            $transfersQuery->groupStart()
                ->like('item_order.asset_num', $filters['search'])
                ->orLike('items.name', $filters['search'])
                ->groupEnd();
        }

        $transfers = $transfersQuery->orderBy('transfer_items.created_at', 'DESC')->findAll();

        // Get returns from history table (usage_status_id = 2)
        $returnsQuery = $historyModel
            ->select('item_order.asset_num as asset_number, items.name as item_name,
                  history.created_at as last_operation_date, item_order.item_order_id as id,
                  history.usage_status_id, history.id as history_id')
            ->join('item_order', 'item_order.item_order_id = history.item_order_id', 'left')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->where('history.usage_status_id', 2);

        if (!empty($filters['asset_number'])) {
            $returnsQuery->like('item_order.asset_num', $filters['asset_number']);
        }
        if (!empty($filters['item_name'])) {
            $returnsQuery->like('items.name', $filters['item_name']);
        }
        if (!empty($filters['date_from'])) {
            $returnsQuery->where('history.created_at >=', $filters['date_from'] . ' 00:00:00');
        }
        if (!empty($filters['date_to'])) {
            $returnsQuery->where('history.created_at <=', $filters['date_to'] . ' 23:59:59');
        }
        if (!empty($filters['search'])) {
            $returnsQuery->groupStart()
                ->like('item_order.asset_num', $filters['search'])
                ->orLike('items.name', $filters['search'])
                ->groupEnd();
        }

        $returns = $returnsQuery->orderBy('history.created_at', 'DESC')->findAll();

        $operations = [];
        $assetTracker = []; // Track unique assets by asset_number

        // Process transfers first (if filter allows)
        if (empty($filters['operation_type']) || $filters['operation_type'] == 'transfer') {
            foreach ($transfers as $t) {
                // Convert to object if it's an array
                if (is_array($t)) {
                    $t = (object)$t;
                }

                if (!isset($assetTracker[$t->asset_number])) {
                    $operations[] = (object)[
                        'id' => $t->id,
                        'asset_number' => $t->asset_number,
                        'item_name' => $t->item_name,
                        'operation_type' => 'transfer',
                        'last_operation_date' => $t->last_operation_date,
                        'unique_key' => 'transfer_' . $t->transfer_item_id
                    ];
                    $assetTracker[$t->asset_number] = strtotime($t->last_operation_date);
                }
            }
        }

        // Process returns (if filter allows)
        if (empty($filters['operation_type']) || $filters['operation_type'] == 'return') {
            foreach ($returns as $r) {
                // Convert to object if it's an array
                if (is_array($r)) {
                    $r = (object)$r;
                }

                $returnTimestamp = strtotime($r->last_operation_date);

                // Only add if:
                // 1. Asset hasn't been seen yet, OR
                // 2. This return is more recent than the tracked operation
                if (!isset($assetTracker[$r->asset_number]) || $returnTimestamp > $assetTracker[$r->asset_number]) {
                    // If asset already exists, remove the old entry
                    if (isset($assetTracker[$r->asset_number])) {
                        $operations = array_filter($operations, function ($op) use ($r) {
                            return $op->asset_number !== $r->asset_number;
                        });
                        $operations = array_values($operations); // Re-index array
                    }

                    $operations[] = (object)[
                        'id' => $r->id,
                        'asset_number' => $r->asset_number,
                        'item_name' => $r->item_name,
                        'operation_type' => 'return',
                        'last_operation_date' => $r->last_operation_date,
                        'usage_status_id' => $r->usage_status_id,
                        'unique_key' => 'return_' . $r->history_id
                    ];
                    $assetTracker[$r->asset_number] = $returnTimestamp;
                }
            }
        }

        // Sort all operations by date descending
        usort($operations, fn($a, $b) => strtotime($b->last_operation_date) - strtotime($a->last_operation_date));

        $uniqueAssets = array_unique(array_column($operations, 'asset_number'));

        $stats = [
            'total_transfers' => count($transfers),
            'total_returns' => count($returns),
            'total_operations' => count($operations),
            'total_assets' => count($uniqueAssets),
        ];

        $perPage = 20;
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $perPage;
        $paginatedOperations = array_slice($operations, $offset, $perPage);

        $pager = \Config\Services::pager();
        $pager->store('operations', $page, $perPage, count($operations));

        return view('assets/super_assets_view', [
            'operations' => $paginatedOperations,
            'stats' => $stats,
            'filters' => $filters,
            'pager' => $pager,
        ]);
    }

    public function assetCycle($assetNum = null): string | ResponseInterface
    {
        if (!session()->get('isLoggedIn')) {
            throw new AuthenticationException();
        }

        if (!$assetNum) {
            $assetNum = $this->request->getGet('asset_num');
        }

        if (!$assetNum) {
            return redirect()->back()->with('error', 'رقم الأصل مطلوب');
        }

        $itemOrderModel = new ItemOrderModel();
        $transferItemsModel = new TransferItemsModel();
        $historyModel = new HistoryModel();
        $employeeModel = new EmployeeModel();
        $usersModel = new UserModel();

        // Get asset info
        $assetInfo = $itemOrderModel
            ->select('item_order.item_order_id, item_order.asset_num, item_order.created_at, 
                      item_order.usage_status_id, items.name as item_name')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->where('item_order.asset_num', $assetNum)
            ->first();

        if (!$assetInfo) {
            return redirect()->back()->with('error', 'الأصل غير موجود');
        }

        // Get all regular transfers
        $transfers = $transferItemsModel
            ->select('transfer_items.*, from_user.name as from_user_name, from_user.user_dept as from_user_dept,
                      from_user.user_ext as from_user_ext, to_user.name as to_user_name, to_user.user_dept as to_user_dept,
                      to_user.user_ext as to_user_ext, order_status.status as status_name')
            ->join('users as from_user', 'from_user.user_id = transfer_items.from_user_id', 'left')
            ->join('users as to_user', 'to_user.user_id = transfer_items.to_user_id', 'left')
            ->join('order_status', 'order_status.id = transfer_items.order_status_id', 'left')
            ->where('transfer_items.item_order_id', $assetInfo->item_order_id)
            ->orderBy('transfer_items.created_at', 'ASC')
            ->findAll();

        // Get history records for return cycles
        $historyRecords = $historyModel
            ->where('item_order_id', $assetInfo->item_order_id)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        $timeline = [];

        // Add regular transfers
        foreach ($transfers as $t) {
            $timeline[] = [
                'type' => 'transfer',
                'transfer_id' => $t->transfer_item_id,
                'date' => $t->created_at,
                'from_user_name' => $t->from_user_name ?? 'غير محدد',
                'from_user_dept' => $t->from_user_dept ?? 'غير محدد',
                'from_user_ext' => $t->from_user_ext ?? '-',
                'to_user_name' => $t->to_user_name ?? 'غير محدد',
                'to_user_dept' => $t->to_user_dept ?? 'غير محدد',
                'to_user_ext' => $t->to_user_ext ?? '-',
                'status' => $t->status_name ?? 'غير محدد',
                'status_date' => $t->updated_at,
                'note' => $t->note
            ];
        }

        // Process history for return cycles
        $returnCycles = [];
        $currentPhase1 = null;
        $warehouseHandlerName = null;

        foreach ($historyRecords as $index => $history) {
            $statusId = (int)$history['usage_status_id'];

            if ($statusId === 2) {
                // Phase 1: User returns to warehouse
                // Save previous cycle if exists
                if ($currentPhase1 !== null) {
                    $returnCycles[] = ['phase_1' => $currentPhase1];
                }

                // Get user info from handled_by
                $userInfo = $this->getUserInfo($history['handled_by'], $usersModel, $employeeModel);

                $currentPhase1 = [
                    'type' => 'return_phase_1',
                    'date' => $history['created_at'],
                    'from_user_name' => $userInfo['name'],
                    'from_user_dept' => $userInfo['dept'],
                    'from_user_ext' => $userInfo['ext'],
                    'warehouse_response' => 'pending',
                    'warehouse_response_status' => 'قيد الانتظار',
                    'warehouse_response_date' => null,
                    'warehouse_handler' => null
                ];
            } elseif (in_array($statusId, [4, 5]) && $currentPhase1 !== null) {
                // Warehouse response (accepted=4 or rejected=5)
                $warehouseInfo = $this->getUserInfo($history['handled_by'], $usersModel, $employeeModel);
                $warehouseHandlerName = $warehouseInfo['name']; // Store for Phase 2

                $currentPhase1['warehouse_response'] = $statusId === 4 ? 'accepted' : 'rejected';
                $currentPhase1['warehouse_response_status'] = $statusId === 4 ? 'مقبول' : 'مرفوض';
                $currentPhase1['warehouse_response_date'] = $history['created_at'];
                $currentPhase1['warehouse_handler'] = $warehouseHandlerName;

                // Add phase 1
                $returnCycles[] = ['phase_1' => $currentPhase1];

                // Phase 2 removed - regular transfers will handle warehouse to user transfers

                $currentPhase1 = null;
            }
        }

        // Add any remaining phase
        if ($currentPhase1 !== null) {
            $returnCycles[] = ['phase_1' => $currentPhase1];
        }

        // Add return cycles to timeline
        foreach ($returnCycles as $cycle) {
            if (isset($cycle['phase_1'])) {
                $timeline[] = $cycle['phase_1'];
            }
            if (isset($cycle['phase_2'])) {
                $timeline[] = $cycle['phase_2'];
            }
        }

        // Sort timeline by date
        usort($timeline, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return view('assets/assets_cycle', [
            'asset_info' => $assetInfo,
            'timeline' => $timeline,
            'total_operations' => count($timeline)
        ]);
    }

    private function getUserInfo($userId, $usersModel, $employeeModel)
    {
        // Try users table first
        $user = $usersModel->where('user_id', $userId)->first();
        if ($user) {
            return [
                'name' => $user->name,
                'dept' => $user->user_dept,
                'ext' => $user->user_ext
            ];
        }

        // Try employee table
        $employee = $employeeModel->where('emp_id', $userId)->first();
        if ($employee) {
            return [
                'name' => $employee->name,
                'dept' => $employee->emp_dept,
                'ext' => $employee->emp_ext
            ];
        }

        return [
            'name' => 'غير محدد',
            'dept' => 'غير محدد',
            'ext' => '-'
        ];
    }

    public function viewDetails($id)
    {
        if (!session()->get('isLoggedIn')) {
            throw new AuthenticationException();
        }
    }

    public function printReturnReport($assetNum = null): ResponseInterface
    {
        if (!session()->get('isLoggedIn')) {
            throw new AuthenticationException();
        }

        if (!$assetNum) {
            return redirect()->back()->with('error', 'رقم الأصل مطلوب');
        }

        // Get item_order_id for this asset
        $itemOrderModel = new ItemOrderModel();
        $assetInfo = $itemOrderModel->where('asset_num', $assetNum)->first();
        
        if (!$assetInfo) {
            return redirect()->back()->with('error', 'الأصل غير موجود');
        }

        // Check for evaluation notes - Get the most recent one
        $evaluationModel = new EvaluationModel();
        $evaluation = $evaluationModel
            ->where('item_order_id', $assetInfo->item_order_id)
            ->orderBy('created_at', 'DESC')
            ->first();
        
        $technicianNotes = isset($evaluation['notes']) ? trim($evaluation['notes']) : null;

        // Define uploads path
        $uploadsPath = WRITEPATH . 'uploads/return_attachments/';
        
        // Search for HTML files matching this asset number
        $files = glob($uploadsPath . 'form_' . $assetNum . '_*.html');
        
        if (empty($files)) {
            return redirect()->back()->with('error', 'لم يتم العثور على تقرير الإرجاع لهذا الأصل');
        }
        
        // Get the most recent file
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        $htmlFile = $files[0];
        $htmlContent = file_get_contents($htmlFile);
        
        // If technician notes exist, inject them into the HTML
        if (!empty($technicianNotes)) {
            // Create the notes section with proper spacing and thinner border
            $notesSection = '
        
        <table style="width: 100%; margin-top: 30px; margin-bottom: 20px; border-collapse: collapse; page-break-inside: avoid;">
            <tr>
                <td style="border: 1px solid #000; padding: 12px; background-color: #e0e0e0; font-weight: bold; text-align: right;">
                    ملاحظات الفني
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 15px 0; min-height: 80px; vertical-align: top; background-color: transparent; text-align: right; line-height: 1.8;">
                    ' . nl2br(htmlspecialchars($technicianNotes, ENT_QUOTES, 'UTF-8')) . '
                </td>
            </tr>
        </table>';
 
            // Find the "المستلم / أمين المستودع" table and insert notes after it
            $searchPattern = '/<table[^>]*>.*?المستلم.*?أمين.*?المستودع.*?<\/table>/s';
            
            if (preg_match($searchPattern, $htmlContent, $matches, PREG_OFFSET_CAPTURE)) {
                // Insert right after the matched table
                $insertPosition = $matches[0][1] + strlen($matches[0][0]);
                $htmlContent = substr_replace($htmlContent, $notesSection, $insertPosition, 0);
            } else {
                // Fallback: try to find by the pink warning box and insert before it
                $warningPattern = '/<table[^>]*background-color:\s*#f8d7da[^>]*>.*?<\/table>/s';
                if (preg_match($warningPattern, $htmlContent, $matches, PREG_OFFSET_CAPTURE)) {
                    $insertPosition = $matches[0][1];
                    $htmlContent = substr_replace($htmlContent, $notesSection, $insertPosition, 0);
                }
            }
        }
        
        return $this->response
            ->setContentType('text/html')
            ->setBody($htmlContent);
    }

    public function assetsHistory(): string
    {
        if (!session()->get('isLoggedIn')) {
            throw new AuthenticationException();
        }
        $itemOrderModel = new ItemOrderModel();
        $transferItemsModel = new TransferItemsModel();

        $userRole = session()->get('role');
        $userId = session()->get('employee_id') ?? session()->get('user_id');

        $toUserIdFilter = $this->request->getGet('to_user_id');

        // Allow super_assets and super_warehouse to view all users' data
        if (!in_array($userRole, ['assets', 'super_assets', 'super_warehouse'])) {
            $toUserIdFilter = $userId;
        }

        // CRITICAL: Force operation_type to 'return' for super_warehouse
        $operationTypeFilter = $this->request->getGet('operation_type');
        if ($userRole === 'super_warehouse') {
            $operationTypeFilter = 'return'; // Force return operations only
        }

        $filters = [
            'search' => $this->request->getGet('search'),
            'asset_number' => $this->request->getGet('asset_number'),
            'item_name' => $this->request->getGet('item_name'),
            'operation_type' => $operationTypeFilter,
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'to_user_id' => $toUserIdFilter,
            'user_role' => $userRole,
        ];

        // ==================== NEW ITEMS ====================
        // Super warehouse cannot see new items
        $newItems = [];
        if ($userRole !== 'super_warehouse') {
            $newItemsQuery = $itemOrderModel
                ->select('item_order.asset_num as asset_number, items.name as item_name,
                      item_order.created_at as last_operation_date, item_order.item_order_id as id,
                      item_order.order_id, order_status.status as order_status_name,
                      order.to_user_id')
                ->join('items', 'items.id = item_order.item_id', 'left')
                ->join('order', 'order.order_id = item_order.order_id', 'left')
                ->join('order_status', 'order_status.id = order.order_status_id', 'left')
                ->where('item_order.usage_status_id', 1);

            if (!empty($filters['asset_number'])) $newItemsQuery->like('item_order.asset_num', $filters['asset_number']);
            if (!empty($filters['item_name'])) $newItemsQuery->like('items.name', $filters['item_name']);
            if (!empty($filters['date_from'])) $newItemsQuery->where('item_order.created_at >=', $filters['date_from'] . ' 00:00:00');
            if (!empty($filters['date_to'])) $newItemsQuery->where('item_order.created_at <=', $filters['date_to'] . ' 23:59:59');
            if (!empty($filters['search'])) {
                $newItemsQuery->groupStart()
                    ->like('item_order.asset_num', $filters['search'])
                    ->orLike('items.name', $filters['search'])
                    ->groupEnd();
            }
            if (!empty($filters['to_user_id'])) {
                $newItemsQuery->where('order.to_user_id', $filters['to_user_id']);
            }

            $newItems = $newItemsQuery->orderBy('item_order.created_at', 'DESC')->findAll();
        }

        // ==================== TRANSFERS ====================
        // Super warehouse cannot see transfers
        $transfers = [];
        if ($userRole !== 'super_warehouse') {
            $transfersQuery = $transferItemsModel
                ->select('item_order.asset_num as asset_number, items.name as item_name,
                    transfer_items.created_at as last_operation_date, item_order.item_order_id as id,
                    item_order.order_id, order_status.status as order_status_name,
                    transfer_items.to_user_id, transfer_items.from_user_id')
                ->join('item_order', 'item_order.item_order_id = transfer_items.item_order_id', 'left')
                ->join('items', 'items.id = item_order.item_id', 'left')
                ->join('order', 'order.order_id = item_order.order_id', 'left')
                ->join('order_status', 'order_status.id = transfer_items.order_status_id', 'left');

            if (!empty($filters['asset_number'])) $transfersQuery->like('item_order.asset_num', $filters['asset_number']);
            if (!empty($filters['item_name'])) $transfersQuery->like('items.name', $filters['item_name']);
            if (!empty($filters['date_from'])) $transfersQuery->where('transfer_items.created_at >=', $filters['date_from'] . ' 00:00:00');
            if (!empty($filters['date_to'])) $transfersQuery->where('transfer_items.created_at <=', $filters['date_to'] . ' 23:59:59');
            if (!empty($filters['search'])) {
                $transfersQuery->groupStart()
                    ->like('item_order.asset_num', $filters['search'])
                    ->orLike('items.name', $filters['search'])
                    ->groupEnd();
            }
            
            // BIDIRECTIONAL SEARCH: Show transfers for BOTH sender and receiver
            if (!empty($filters['to_user_id'])) {
                $transfersQuery->groupStart()
                    ->where('transfer_items.to_user_id', $filters['to_user_id'])  // المستلم
                    ->orWhere('transfer_items.from_user_id', $filters['to_user_id'])  // المرسل
                    ->groupEnd();
            }

            $transfers = $transfersQuery->orderBy('transfer_items.created_at', 'DESC')->findAll();
        }

        // ==================== RETURNS ====================
        // All roles can see returns - NOW WITH BIDIRECTIONAL SEARCH
        $returnsQuery = $itemOrderModel
            ->select('item_order.asset_num as asset_number, items.name as item_name,
                  item_order.updated_at as last_operation_date, item_order.item_order_id as id,
                  item_order.created_by, order.to_user_id, item_order.order_id, item_order.usage_status_id,
                  created_employee.name as created_employee_name, created_employee.emp_id as created_emp_id,
                  created_user.name as created_user_name, created_user.user_id as created_user_id,
                  returner_employee.name as returner_employee_name, returner_employee.emp_id as returner_emp_id,
                  returner_user.name as returner_user_name, returner_user.user_id as returner_user_id')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->join('order', 'order.order_id = item_order.order_id', 'left')
            // Join for person who CREATED the return (created_by from item_order)
            ->join('employee as created_employee', 'created_employee.emp_id = item_order.created_by', 'left')
            ->join('users as created_user', 'created_user.user_id = item_order.created_by', 'left')
            // Join for person who is RETURNING the asset (to_user_id from order table)
            ->join('employee as returner_employee', 'returner_employee.emp_id = order.to_user_id', 'left')
            ->join('users as returner_user', 'returner_user.user_id = order.to_user_id', 'left')
            ->whereIn('item_order.usage_status_id', [2, 4, 5, 7]);

        if (!empty($filters['asset_number'])) $returnsQuery->like('item_order.asset_num', $filters['asset_number']);
        if (!empty($filters['item_name'])) $returnsQuery->like('items.name', $filters['item_name']);
        if (!empty($filters['date_from'])) $returnsQuery->where('item_order.updated_at >=', $filters['date_from'] . ' 00:00:00');
        if (!empty($filters['date_to'])) $returnsQuery->where('item_order.updated_at <=', $filters['date_to'] . ' 23:59:59');
        if (!empty($filters['search'])) {
            $returnsQuery->groupStart()
                ->like('item_order.asset_num', $filters['search'])
                ->orLike('items.name', $filters['search'])
                ->groupEnd();
        }
        
        // BIDIRECTIONAL SEARCH: Search in BOTH directions
        if (!empty($filters['to_user_id'])) {
            $returnsQuery->groupStart()
                // Person RETURNING the asset (to_user_id from order table)
                ->like('returner_employee.emp_id', $filters['to_user_id'])
                ->orLike('returner_user.user_id', $filters['to_user_id'])
                ->orLike('order.to_user_id', $filters['to_user_id'])
                // Person who CREATED/RECEIVED the return (created_by from item_order)
                ->orLike('created_employee.emp_id', $filters['to_user_id'])
                ->orLike('created_user.user_id', $filters['to_user_id'])
                ->orLike('item_order.created_by', $filters['to_user_id'])
                ->groupEnd();
        }

        $returns = $returnsQuery->orderBy('item_order.updated_at', 'DESC')->findAll();

        // ==================== FINAL MERGE ====================
        $operations = [];
        $uniqueAssets = [];

        // NEW ITEMS - Only for non-super_warehouse
        if ($userRole !== 'super_warehouse' && (empty($filters['operation_type']) || $filters['operation_type'] == 'new')) {
            foreach ($newItems as $n) {
                if (!isset($uniqueAssets[$n->asset_number])) {
                    $operations[] = (object)[
                        'id' => $n->id,
                        'asset_number' => $n->asset_number,
                        'item_name' => $n->item_name,
                        'operation_type' => 'new',
                        'last_operation_date' => $n->last_operation_date,
                        'order_status_name' => $n->order_status_name ?? 'غير محدد',
                        'usage_status_id' => null
                    ];
                    $uniqueAssets[$n->asset_number] = true;
                }
            }
        }

        // TRANSFERS - Only for non-super_warehouse
        if ($userRole !== 'super_warehouse' && (empty($filters['operation_type']) || $filters['operation_type'] == 'transfer')) {
            foreach ($transfers as $t) {
                if (!isset($uniqueAssets[$t->asset_number])) {
                    $operations[] = (object)[
                        'id' => $t->id,
                        'asset_number' => $t->asset_number,
                        'item_name' => $t->item_name,
                        'operation_type' => 'transfer',
                        'last_operation_date' => $t->last_operation_date,
                        'order_status_name' => $t->order_status_name ?? 'غير محدد',
                        'usage_status_id' => null
                    ];
                    $uniqueAssets[$t->asset_number] = true;
                }
            }
        }

        // RETURNS - For all roles
        if (empty($filters['operation_type']) || $filters['operation_type'] == 'return') {
            foreach ($returns as $r) {
                if (!isset($uniqueAssets[$r->asset_number])) {
                    $displayStatus = match ((int)$r->usage_status_id) {
                        2 => 'قيد الانتظار',
                        4 => 'مقبول',
                        5 => 'مرفوض',
                        default => 'غير محدد',
                    };

                    $operations[] = (object)[
                        'id' => $r->id,
                        'asset_number' => $r->asset_number,
                        'item_name' => $r->item_name,
                        'operation_type' => 'return',
                        'last_operation_date' => $r->last_operation_date,
                        'order_status_name' => $displayStatus,
                        'usage_status_id' => $r->usage_status_id
                    ];
                    $uniqueAssets[$r->asset_number] = true;
                }
            }
        }

        // Sort by latest operation date
        usort($operations, fn($a, $b) => strtotime($b->last_operation_date) - strtotime($a->last_operation_date));

        $perPage = 10;
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $perPage;
        $paginatedOperations = array_slice($operations, $offset, $perPage);

        $pager = \Config\Services::pager();
        $pager->store('operations', $page, $perPage, count($operations));

        return view('assets/assets_history', [
            'operations' => $paginatedOperations,
            'stats' => [
                'total_new' => count($newItems),
                'total_transfers' => count($transfers),
                'total_returns' => count($returns),
                'total_operations' => count($operations),
                'total_assets' => count($uniqueAssets),
            ],
            'filters' => $filters,
            'pager' => $pager,
        ]);
    }
}