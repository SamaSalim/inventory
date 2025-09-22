<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ItemOrderModel;
use App\Models\MinorCategoryModel;
use App\Models\ItemModel;
use App\Models\OrderStatusModel;
use App\Models\UsageStatusModel;
use App\Exceptions\AuthenticationException;
use App\Entities\Item;
use App\Entities\ItemOrder;

class UserController extends BaseController
{
    protected $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

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
     * -    userView جدول  
     */
    public function dashboard(): string
    {
        $this->checkAuth(); // تحقق من تسجيل الدخول


        $itemOrderModel = new ItemOrderModel();

        $itemOrders = $itemOrderModel
            ->distinct()
            ->select(
                'item_order.order_id, 
                 item_order.created_at, 
                 item_order.created_by, 
                 room.code AS room_code, 
                 employee.name AS created_by_name, 
                 employee.emp_id AS employee_id, 
                 employee.emp_ext AS extension'
            )
            ->join('employee', 'employee.emp_id = item_order.created_by', 'left')
            ->join('room', 'room.id = item_order.room_id', 'left')
            ->groupBy('item_order.order_id')
            ->orderBy('item_order.created_at', 'DESC')
            ->findAll();

        $minorCategoryModel = new MinorCategoryModel();
        $categories = $minorCategoryModel->select('minor_category.*, major_category.name AS major_category_name')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->findAll();

     
        $stats = $this->getWarehouseStats();

       
        $orderStatusModel = new OrderStatusModel();
        $statuses = $orderStatusModel->findAll();

        $usageStatusModel = new UsageStatusModel();
        $usageStatuses = $usageStatusModel->findAll();

        return view('user/userView', [
            'categories' => $categories,
            'orders' => $itemOrders,
            'stats' => $stats,
            'statuses' => $statuses,
            'usage_statuses' => $usageStatuses
        ]);
    }

  
    private function getWarehouseStats(): array
    {
        $itemOrderModel = new ItemOrderModel();
        $itemModel = new ItemModel();

        $totalQuantityResult = $itemOrderModel->selectSum('quantity')->first();
        $totalReceipts = $totalQuantityResult ? (int)$totalQuantityResult->quantity : 0;

        $availableItems = $itemOrderModel->countAllResults();
        $totalEntries = $itemModel->countAllResults();

        $lowStock = $itemOrderModel->where('quantity <', 10)
            ->where('quantity >', 0)
            ->countAllResults();

        $topCategoryResult = $itemOrderModel->select('items.minor_category_id, minor_category.name, COUNT(*) as count')
            ->join('items', 'items.id = item_order.item_id')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->groupBy('items.minor_category_id')
            ->orderBy('count', 'DESC')
            ->first();
        $topCategory = $topCategoryResult ? $topCategoryResult->name : 'غير محدد';

        $lastEntry = $itemOrderModel->select('item_order.created_at, items.name')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->orderBy('item_order.created_at', 'DESC')
            ->first();

        return [
            'total_receipts' => $totalReceipts,
            'available_items' => $availableItems,
            'total_entries' => $totalEntries,
            'low_stock' => $lowStock,
            'top_category' => $topCategory,
            'last_entry' => $lastEntry ? [
                'item' => $lastEntry->name ?? 'غير محدد',
                'date' => date('Y-m-d H:i', strtotime($lastEntry->created_at))
            ] : null
        ];
    }

    /**
     * عرض تفاصيل طلب محدد
     */
    public function showOrder($order_id)
    {
        $this->checkAuth(); // تحقق من تسجيل الدخول

        $itemOrderModel = new ItemOrderModel();
        $order = $itemOrderModel->find($order_id);

        if (!$order) {
            return redirect()->back()->with('error', 'الطلب غير موجود');
        }

        $data['order'] = $order;
        return view('warehouse/showOrderView', $data); // فيو تفصيلي للطلب
    }
}
