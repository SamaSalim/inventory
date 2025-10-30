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
use CodeIgniter\HTTP\RedirectResponse;

use App\Models\TransferItemsModel;

class UserController extends BaseController
{
    protected $orderModel;
    protected $transferItemsModel;
    protected $itemOrderModel;
    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->transferItemsModel = new TransferItemsModel();
        $this->itemOrderModel = new ItemOrderModel();
    }

    /**
     * دالة خاصة للتحقق من تسجيل الدخول
     */
    private function checkAuth()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
    }

    /**
     * عرض الطلبات المحولة للمستخدم الحالي
     */
    public function dashboard(): string
    {
        $this->checkAuth();

        // التحقق من تسجيل الدخول
       

        // جلب معلومات الحساب من السيشن (نفس طريقة UserInfo)
        $isEmployee = session()->get('isEmployee');
        $account_id = $isEmployee ? session()->get('employee_id') : session()->get('user_id'); // يحتوي على user_id أو emp_id

        // تحديد user_id بناءً على نوع الحساب
        //  $currentUserId = null;

        // if (!$isEmployee) {
        // إذا كان مستخدم عادي، account_id هو user_id مباشرة
        $currentUserId = $account_id;
        // }
        //  echo $account_id;
        $transferItemsModel = new TransferItemsModel();

        // استعلام الطلبات المحولة للمستخدم الحالي
        $myOrders = $transferItemsModel
            ->distinct()
            ->select(
                'transfer_items.transfer_item_id,
                 transfer_items.created_at,
                 transfer_items.item_order_id,
                 transfer_items.is_opened, 
                 item_order.created_by AS employee_id,
                 item_order.asset_num,
                 item_order.serial_num,
                 from_user.name AS from_user_name,
                 from_user.user_dept AS from_user_dept,
                 usage_status.usage_status AS usage_status_name,
                 order_status.status AS order_status_name'
            )
            ->join('item_order', 'item_order.item_order_id = transfer_items.item_order_id', 'left')
            ->join('users AS from_user', 'from_user.user_id = transfer_items.from_user_id', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
            ->join('order_status', 'order_status.id = transfer_items.order_status_id', 'left')
            ->where('transfer_items.to_user_id', $currentUserId)
            ->where('item_order.usage_status_id !=', 2) // استبعاد العناصر المرجعة
            ->where('transfer_items.order_status_id', 1) // إظهار الطلبات قيد الانتظار فقط
            ->orderBy('transfer_items.created_at', 'DESC')
            ->findAll();

        return view('user/userView', [
            'orders' => $myOrders
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
     * جلب تفاصيل العهدة  
     */
    public function getTransferDetails($transferId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $currentUserId = session()->get('isEmployee') ? session()->get('employee_id') : session()->get('user_id');

        $transferModel = new TransferItemsModel();
        $itemOrderModel = new ItemOrderModel();

        // جلب معلومات الطلب الأساسية
        $transfer = $transferModel
            ->select('transfer_items.*, from_user.name AS from_user_name, from_user.user_dept AS from_user_dept, 
                  from_user.user_ext AS from_user_ext, from_user.email AS from_user_email, 
                  order_status.status AS order_status_name, item_order.order_id')
            ->join('item_order', 'item_order.item_order_id = transfer_items.item_order_id', 'left')
            ->join('users AS from_user', 'from_user.user_id = transfer_items.from_user_id', 'left')
            ->join('order_status', 'order_status.id = transfer_items.order_status_id', 'left')
            ->where('transfer_items.transfer_item_id', $transferId)
            ->where('transfer_items.to_user_id', $currentUserId)
            ->first();

        if (!$transfer) {
            return $this->response->setJSON(['success' => false, 'message' => 'الطلب غير موجود']);
        }

        // جلب الأصناف من transfer_items
        $transferItems = $itemOrderModel
            ->select('item_order.item_order_id, item_order.asset_num, item_order.serial_num, item_order.assets_type,
                  items.name as item_name, usage_status.usage_status AS usage_status_name')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
            ->join('transfer_items', 'transfer_items.item_order_id = item_order.item_order_id', 'left')
            ->where('transfer_items.to_user_id', $currentUserId)
            ->where('item_order.order_id', $transfer->order_id)
            ->findAll();

        // جلب الأصناف من order
        $orderItems = $itemOrderModel
            ->select('item_order.item_order_id, item_order.asset_num, item_order.serial_num, item_order.assets_type,
                  items.name as item_name, usage_status.usage_status AS usage_status_name')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
            ->join('order', 'order.order_id = item_order.order_id', 'left')
            ->where('order.to_user_id', $currentUserId)
            ->where('item_order.order_id', $transfer->order_id)
            ->findAll();

        // دمج وإزالة التكرار
        $items = array_values(array_unique(array_merge($transferItems, $orderItems), SORT_REGULAR));

        return $this->response->setJSON(['success' => true, 'data' => $transfer, 'items' => $items]);
    }

    /**
     * قبول أو رفض طلب العهدة
     */
    public function respondToTransfer()
    {
        $this->response->setContentType('application/json');

        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $json = $this->request->getJSON();

        if (!$json) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'بيانات غير صحيحة'
            ]);
        }

        $transferId = $json->transfer_id ?? null;
        $action = $json->action ?? null;

        if (!$transferId || !$action) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'البيانات المطلوبة غير مكتملة'
            ]);
        }

        try {
            $transferModel = $this->transferItemsModel;
            $itemOrderModel = $this->itemOrderModel;
            $orderModel = $this->orderModel;

            $transfer = $transferModel->find($transferId);

            if (!$transfer) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'الطلب غير موجود'
                ]);
            }


            if ($transfer->order_status_id != 1) {
                $statusText = $transfer->order_status_id == 2 ? 'مقبول' : 'مرفوض';
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'هذا الطلب ' . $statusText . ' مسبقاً'
                ]);
            }

            $orderStatusId = ($action === 'accept') ? 2 : 3;

            $orderModel->transStart();

            $updated = $transferModel->update($transferId, [
                'order_status_id' => $orderStatusId,
                'updated_at' => date('Y-m-d H:i:s'),
                'received_at' => ($action === 'accept') ? date('Y-m-d H:i:s') : null,
            ]);

            if (!$updated) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل تحديث حالة الطلب'
                ]);
            }

            $itemOrder = $itemOrderModel->find($transfer->item_order_id);
            $orderId = $itemOrder->order_id ?? null;
            if (!$orderId) {
                throw new \Exception('فشل تحديد الطلب الرئيسي');
            }

            if ($action === 'accept') {
                $allTransferItems = $transferModel
                    ->join('item_order', 'item_order.item_order_id = transfer_items.item_order_id')
                    ->where('item_order.order_id', $orderId)
                    ->findAll();

                $allAccepted = true;
                foreach ($allTransferItems as $item) {
                    if ($item->order_status_id != 2) {
                        $allAccepted = false;
                        break;
                    }
                }

                if ($allAccepted) {
                    $orderModel->update($orderId, ['order_status_id' => 2]);
                }
            } else {
                $orderModel->update($orderId, ['order_status_id' => 3]);
            }

            $orderModel->transComplete();

            if ($orderModel->transStatus() === FALSE) {
                throw new \Exception('فشل في المعاملة الكلية.');
            }

            $message = ($action === 'accept')
                ? 'تم قبول الطلب بنجاح. العهدة الآن في عهدتك'
                : 'تم رفض الطلب. العهدة ستبقى مع المُرسل';

            return $this->response->setJSON([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Transfer Response Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * تعليم أن الطلب قد تم فتحه (is_opened = 1)
     */
    public function markAsOpened()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $json = $this->request->getJSON();
        $transferId = $json->transfer_id ?? null;

        if (!$transferId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'transfer_id is required'
            ]);
        }

        try {
            $transferModel = new TransferItemsModel();

            $transfer = $transferModel->find($transferId);

            if (!$transfer) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'السجل غير موجود'
                ]);
            }

            $updated = $transferModel->update($transferId, [
                'is_opened' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($updated) {
                log_message('info', "Transfer {$transferId} marked as opened");
                return $this->response->setJSON(['success' => true]);
            } else {
                log_message('error', "Failed to update transfer {$transferId}");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل التحديث'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'markAsOpened error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function userView2(): string
    {
        $this->checkAuth();

       

        // $isEmployee = session()->get('isEmployee');
        // $account_id = session()->get('employee_id');
        // $currentUserId = null;
        $currentUserId = session()->get('isEmployee') ? session()->get('employee_id') : session()->get('user_id');

        // if (!$isEmployee) {
        //     $currentUserId = $account_id;
        // }

        $transferItemsModel = $this->transferItemsModel;

        $transferItems = $transferItemsModel
            ->select(
                'transfer_items.transfer_item_id AS id,
             transfer_items.created_at,
             transfer_items.item_order_id,
             transfer_items.is_opened, 
             item_order.asset_num,
             item_order.serial_num,
             item_order.model_num AS model,
             item_order.brand,
             item_order.old_asset_num,
             item_order.assets_type,
             item_order.usage_status_id,
             items.name AS item_name,
             minor_category.name AS minor_category_name,
             major_category.name AS major_category_name,
             from_user.name AS from_user_name,
             from_user.user_dept AS from_user_dept,
             usage_status.usage_status AS usage_status_name,
             order_status.status AS order_status_name,
             "transfer_items" AS source_table'
            )
            ->join('item_order', 'item_order.item_order_id = transfer_items.item_order_id', 'left')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->join('users AS from_user', 'from_user.user_id = transfer_items.from_user_id', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
            ->join('order_status', 'order_status.id = transfer_items.order_status_id', 'left')
            ->where('transfer_items.to_user_id', $currentUserId)
            ->where('transfer_items.order_status_id',  2)
            ->groupBy('transfer_items.transfer_item_id')
            ->orderBy('transfer_items.created_at', 'ASC')
            ->findAll();

        $orderModel = $this->orderModel;

        $orders = $orderModel
            ->select(
                'order.order_id AS id,
             order.created_at,
             order.to_user_id,
             order.order_status_id,
             order_status.status AS order_status_name,
             usage_status.usage_status AS usage_status_name,
             from_user.name AS from_user_name,
             from_user.user_dept AS from_user_dept,
             item_order.asset_num,
             item_order.serial_num,
             item_order.model_num AS model,
             item_order.brand,
             item_order.old_asset_num,
             item_order.assets_type,
             item_order.item_order_id,
             item_order.usage_status_id,
             items.name AS item_name,
             minor_category.name AS minor_category_name,
             major_category.name AS major_category_name,
             "orders" AS source_table'
            )
            ->join('item_order', 'item_order.order_id = order.order_id', 'left')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->join('users AS from_user', 'from_user.user_id = order.from_user_id', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
            ->join('order_status', 'order_status.id = order.order_status_id', 'left')
            ->where('order.to_user_id', $currentUserId)
            ->where('order.order_status_id', 2)
            ->groupBy('item_order.item_order_id')
            ->orderBy('order.created_at', 'ASC')
            ->findAll();

        $combinedItems = [];
        $assetNums = [];

        foreach ($orders as $order) {
            $assetNum = $order->asset_num;
            $key = $order->item_order_id ?? $assetNum;

            if (!isset($assetNums[$key])) {
                $combinedItems[] = $order;
                $assetNums[$key] = true;
            }
        }

        foreach ($transferItems as $transfer) {
            $assetNum = $transfer->asset_num;
            $key = $transfer->item_order_id ?? $assetNum;

            if (!isset($assetNums[$key])) {
                $combinedItems[] = $transfer;
                $assetNums[$key] = true;
            }
        }

        $filteredOrders = array_filter($combinedItems, function ($item) {
            return isset($item->usage_status_id) && $item->usage_status_id != 2;
        });

        $filteredOrders = array_values($filteredOrders);

        return view('user/userView2', [
            'orders' => $filteredOrders
        ]);
    }
}
