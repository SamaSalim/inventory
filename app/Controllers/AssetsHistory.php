<?php

namespace App\Controllers;

use App\Controllers\BaseController;
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
        if (!session()->get('isLoggedIn')) {
            throw new AuthenticationException();
        }
    }

    /**
     * عرض صفحة return_view مع بيانات الأصول مع فلترة
     */
    public function index(): string
    {
        $this->checkAuth();

        $itemOrderModel = new ItemOrderModel();

        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $usageStatusId = $this->request->getGet('usage_status_id');

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
                 usage_status.usage_status AS usage_status_name,
                 order_status.status AS order_status_name'
            )
            ->join('employee', 'employee.emp_id = item_order.created_by', 'left')
            ->join('room', 'room.id = item_order.room_id', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
            ->join('order', 'order.order_id = item_order.order_id', 'left')
            ->join('order_status', 'order_status.id = order.order_status_id', 'left')
            ->where('usage_status.usage_status LIKE', '%رجيع%'); // <-- فقط الرجيع من حالة الاستخدام

        if ($startDate) {
            $itemOrdersQuery->where('item_order.created_at >=', $startDate . ' 00:00:00');
        }
        if ($endDate) {
            $itemOrdersQuery->where('item_order.created_at <=', $endDate . ' 23:59:59');
        }
        if ($usageStatusId) {
            $itemOrdersQuery->where('item_order.usage_status_id', $usageStatusId);
        }

        $itemOrders = $itemOrdersQuery->orderBy('item_order.created_at', 'DESC')->findAll();

        $minorCategoryModel = new MinorCategoryModel();
        $categories = $minorCategoryModel->select('minor_category.*, major_category.name AS major_category_name')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->findAll();

        $usageStatusModel = new UsageStatusModel();
        $usageStatuses = $usageStatusModel->findAll();

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
     * عرض صفحة super_assets_view - تحتوي على الإحصائيات والتحويلات والإرجاعات
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

        // التحويلات
        $transfersQuery = $transferItemsModel
            ->select('item_order.asset_num as asset_number, items.name as item_name,
                      transfer_items.created_at as last_operation_date, item_order.item_order_id as id')
            ->join('item_order', 'item_order.item_order_id = transfer_items.item_order_id', 'left')
            ->join('items', 'items.id = item_order.item_id', 'left');

        // إضافة فلترة
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

        // الإرجاعات
        $returnsQuery = $itemOrderModel
            ->select('item_order.asset_num as asset_number, items.name as item_name,
                      item_order.updated_at as last_operation_date, item_order.item_order_id as id')
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

        $operations = [];
        $uniqueAssets = [];

        if (empty($filters['operation_type']) || $filters['operation_type'] == 'transfer') {
            foreach ($transfers as $t) {
                if (!isset($uniqueAssets[$t->asset_number])) {
                    $operations[] = (object)[
                        'id' => $t->id,
                        'asset_number' => $t->asset_number,
                        'item_name' => $t->item_name,
                        'operation_type' => 'transfer',
                        'last_operation_date' => $t->last_operation_date,
                    ];
                    $uniqueAssets[$t->asset_number] = true;
                }
            }
        }

        if (empty($filters['operation_type']) || $filters['operation_type'] == 'return') {
            foreach ($returns as $r) {
                if (!isset($uniqueAssets[$r->asset_number])) {
                    $operations[] = (object)[
                        'id' => $r->id,
                        'asset_number' => $r->asset_number,
                        'item_name' => $r->item_name,
                        'operation_type' => 'return',
                        'last_operation_date' => $r->last_operation_date,
                    ];
                    $uniqueAssets[$r->asset_number] = true;
                }
            }
        }

        // ترتيب حسب التاريخ
        usort($operations, fn($a, $b) => strtotime($b->last_operation_date) - strtotime($a->last_operation_date));

        $perPage = 20;
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $perPage;
        $paginatedOperations = array_slice($operations, $offset, $perPage);

        $pager = \Config\Services::pager();
        $pager->store('operations', $page, $perPage, count($operations));

        return view('assets/super_assets_view', [
            'operations' => $paginatedOperations,
            'stats' => [
                'total_transfers' => count($transfers),
                'total_returns' => count($returns),
                'total_operations' => count($operations),
                'total_assets' => count($uniqueAssets),
            ],
            'filters' => $filters,
            'pager' => $pager,
        ]);
    }


public function assetCycle($assetNum = null): string
{
    $this->checkAuth();

    if (!$assetNum) {
        $assetNum = $this->request->getGet('asset_num');
    }

    if (!$assetNum) {
        return redirect()->back()->with('error', 'رقم الأصل مطلوب');
    }

    $itemOrderModel = new ItemOrderModel();
    $transferItemsModel = new TransferItemsModel();

    $assetInfo = $itemOrderModel
        ->select('item_order.item_order_id, item_order.asset_num, item_order.created_at,
                  item_order.usage_status_id, items.name as item_name, usage_status.usage_status as status_name')
        ->join('items', 'items.id = item_order.item_id', 'left')
        ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
        ->where('item_order.asset_num', $assetNum)
        ->first();

    if (!$assetInfo) {
        return redirect()->back()->with('error', 'الأصل غير موجود');
    }

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

    $timeline = [];

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
            'note' => $t->note,
            'is_opened' => $t->is_opened
        ];
    }

    if ($assetInfo->usage_status_id == 2) {
        $db = \Config\Database::connect();
        
        $returnInfo = $db->table('returned_items')
            ->select('returned_items.*')
            ->where('returned_items.item_order_id', $assetInfo->item_order_id)
            ->get()->getRow();

        $lastTransfer = $transferItemsModel
            ->select('transfer_items.*, users.name as user_name, users.user_dept, users.user_ext')
            ->join('users', 'users.user_id = transfer_items.to_user_id', 'left')
            ->where('transfer_items.item_order_id', $assetInfo->item_order_id)
            ->orderBy('transfer_items.created_at', 'DESC')
            ->first();

        if (!$lastTransfer) {
            $creatorInfo = $db->table('item_order')
                ->select('item_order.created_by, employee.name as user_name, 
                         employee.emp_dept as user_dept, employee.emp_ext as user_ext')
                ->join('employee', 'employee.emp_id = item_order.created_by', 'left')
                ->where('item_order.item_order_id', $assetInfo->item_order_id)
                ->get()->getRow();

            $timeline[] = [
                'type' => 'returned',
                'date' => $returnInfo ? $returnInfo->return_date : $assetInfo->updated_at,
                'status_date' => $returnInfo ? $returnInfo->return_date : $assetInfo->updated_at,
                'note' => $returnInfo ? $returnInfo->notes : 'تم إرجاع الأصل إلى المستودع',
                'returned_by_name' => $creatorInfo->user_name ?? 'غير محدد',
                'returned_by_dept' => $creatorInfo->user_dept ?? 'غير محدد',
                'returned_by_ext' => $creatorInfo->user_ext ?? '-',
                'status' => 'تم الإرجاع'
            ];
        } else {
            $timeline[] = [
                'type' => 'returned',
                'date' => $returnInfo ? $returnInfo->return_date : $assetInfo->updated_at,
                'status_date' => $returnInfo ? $returnInfo->return_date : $assetInfo->updated_at,
                'note' => $returnInfo ? $returnInfo->notes : 'تم إرجاع الأصل إلى المستودع',
                'returned_by_name' => $lastTransfer->user_name ?? 'غير محدد',
                'returned_by_dept' => $lastTransfer->user_dept ?? 'غير محدد',
                'returned_by_ext' => $lastTransfer->user_ext ?? '-',
                'status' => 'تم الإرجاع'
            ];
        }
    }

    return view('assets/assets_cycle', [
        'asset_info' => $assetInfo,
        'timeline' => $timeline,
        'total_operations' => count($timeline)
    ]);
}
    /**
     * (اختياري لاحقاً) عرض تفاصيل العملية الواحدة
     */
    public function viewDetails($id)
    {
        $this->checkAuth();
        // يمكنك تطوير التفاصيل لاحقاً
    }
}