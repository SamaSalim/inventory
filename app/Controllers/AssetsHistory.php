<?php

namespace App\Controllers;

use App\Models\ItemOrderModel;
use App\Models\TransferItemsModel;
use App\Models\MinorCategoryModel;
use App\Models\UsageStatusModel;
use App\Exceptions\AuthenticationException;

class AssetsHistory extends BaseController
{
    /**
     * دالة خاصة للتحقق من تسجيل الدخول
     */
    private function checkAuth()
    {
        if (! session()->get('isLoggedIn')) {
            throw new AuthenticationException();
        }
    }

    /**
     * عرض صفحة return_view مع بيانات الأصول مع فلترة
     */
    public function index(): string
    {
        $this->checkAuth(); // تحقق من تسجيل الدخول

        $itemOrderModel = new ItemOrderModel();

        // الحصول على قيم الفلترة من الطلب (GET أو POST)
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $usageStatusId = $this->request->getGet('usage_status_id');

        // بناء الاستعلام
        $itemOrdersQuery = $itemOrderModel
            ->distinct()
            ->select(
                'item_order.order_id, 
                 item_order.created_at, 
                 item_order.created_by, 
                 room.code AS room_code, 
                 employee.name AS created_by_name, 
                 employee.emp_id AS employee_id, 
                 employee.emp_ext AS extension,
                 usage_status.usage_status AS usage_status_name'
            )
            ->join('employee', 'employee.emp_id = item_order.created_by', 'left')
            ->join('room', 'room.id = item_order.room_id', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left');

        // تطبيق الفلترة حسب التاريخ إذا موجودة
        if ($startDate) {
            $itemOrdersQuery->where('item_order.created_at >=', $startDate . ' 00:00:00');
        }
        if ($endDate) {
            $itemOrdersQuery->where('item_order.created_at <=', $endDate . ' 23:59:59');
        }

        // فلترة حسب حالة الاستخدام إذا محددة
        if ($usageStatusId) {
            $itemOrdersQuery->where('item_order.usage_status_id', $usageStatusId);
        }

        // ترتيب البيانات
        $itemOrders = $itemOrdersQuery->orderBy('item_order.created_at', 'DESC')->findAll();

        // جلب الفئات
        $minorCategoryModel = new MinorCategoryModel();
        $categories = $minorCategoryModel->select('minor_category.*, major_category.name AS major_category_name')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->findAll();

        // حالات الاستخدام
        $usageStatusModel = new UsageStatusModel();
        $usageStatuses = $usageStatusModel->findAll();

        // تمرير البيانات للفيو
        return view('assets/return_view', [
            'categories' => $categories,
            'orders' => $itemOrders,
            'usage_statuses' => $usageStatuses,
            'filter' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'usage_status_id' => $usageStatusId
            ]
        ]);
    }

   




    /**
     * عرض صفحة سوبر استس - سجلات التحويل والإرجاع
     */
    public function superAssets(): string
    {
        $this->checkAuth();

        $itemOrderModel = new ItemOrderModel();
        $transferItemsModel = new TransferItemsModel();

        $filters = [
            'search' => $this->request->getGet('search'),
            'asset_number' => $this->request->getGet('asset_number'),
            'item_name' => $this->request->getGet('item_name'),
            'operation_type' => $this->request->getGet('operation_type'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
        ];

        // 🔹 Fetch transfers
        $transfersQuery = $transferItemsModel
            ->select('
                item_order.asset_num as asset_number,
                items.name as item_name,
                transfer_items.created_at as last_operation_date,
                item_order.item_order_id as id
            ')
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

        // 🔹 Fetch returns
        $returnsQuery = $itemOrderModel
            ->select('
                item_order.asset_num as asset_number,
                items.name as item_name,
                item_order.updated_at as last_operation_date,
                item_order.item_order_id as id
            ')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->where('item_order.usage_status_id', 2);

        if (!empty($filters['asset_number'])) {
            $returnsQuery->like('item_order.asset_num', $filters['asset_number']);
        }
        if (!empty($filters['item_name'])) {
            $returnsQuery->like('items.name', $filters['item_name']);
        }
        if (!empty($filters['date_from'])) {
            $returnsQuery->where('item_order.updated_at >=', $filters['date_from'] . ' 00:00:00');
        }
        if (!empty($filters['date_to'])) {
            $returnsQuery->where('item_order.updated_at <=', $filters['date_to'] . ' 23:59:59');
        }
        if (!empty($filters['search'])) {
            $returnsQuery->groupStart()
                ->like('item_order.asset_num', $filters['search'])
                ->orLike('items.name', $filters['search'])
                ->groupEnd();
        }

        $returns = $returnsQuery->orderBy('item_order.updated_at', 'DESC')->findAll();

        // 🔹 Stats for cards (raw counts)
        $totalTransfers = count($transfers);
        $totalReturns = count($returns);
        $totalOperations = $totalTransfers + $totalReturns;

        // 🔹 Build unique-asset operations for table
        $operations = [];
        $uniqueAssets = [];

        if (empty($filters['operation_type']) || $filters['operation_type'] == 'transfer') {
            foreach ($transfers as $transfer) {
                $assetNum = $transfer->asset_number ?? '-';
                if (!isset($uniqueAssets[$assetNum])) {
                    $operations[] = (object)[
                        'id' => $transfer->id,
                        'asset_number' => $assetNum,
                        'item_name' => $transfer->item_name ?? '-',
                        'operation_type' => 'transfer',
                        'last_operation_date' => $transfer->last_operation_date ?? '-',
                    ];
                    $uniqueAssets[$assetNum] = true;
                }
            }
        }

        if (empty($filters['operation_type']) || $filters['operation_type'] == 'return') {
            foreach ($returns as $return) {
                $assetNum = $return->asset_number ?? '-';
                if (!isset($uniqueAssets[$assetNum])) {
                    $operations[] = (object)[
                        'id' => $return->id,
                        'asset_number' => $assetNum,
                        'item_name' => $return->item_name ?? '-',
                        'operation_type' => 'return',
                        'last_operation_date' => $return->last_operation_date ?? '-',
                    ];
                    $uniqueAssets[$assetNum] = true;
                }
            }
        }

        // Sort by latest operation date
        usort($operations, function ($a, $b) {
            return strtotime($b->last_operation_date) - strtotime($a->last_operation_date);
        });

        // Count unique assets for card
        $totalAssets = count($uniqueAssets);

        $stats = [
            'total_transfers' => $totalTransfers,
            'total_returns' => $totalReturns,
            'total_assets' => $totalAssets,
            'total_operations' => $totalOperations,
        ];

        // Pagination
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

    /**
     * عرض تفاصيل العملية
     */
    public function viewDetails($id)
    {
        $this->checkAuth();
        
        // يمكن تطوير هذه الدالة لاحقاً لعرض تفاصيل العملية
        
    }
}