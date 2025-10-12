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
     * عرض الطلبات المحولة للمستخدم الحالي
     */
    public function dashboard(): string
    {
        $this->checkAuth();

        // التحقق من تسجيل الدخول
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        // جلب معلومات الحساب من السيشن (نفس طريقة UserInfo)
        $isEmployee = session()->get('isEmployee');
        $account_id = session()->get('employee_id'); // يحتوي على user_id أو emp_id

        // تحديد user_id بناءً على نوع الحساب
        // $currentUserId = null;

        // if (!$isEmployee) {
        //     // إذا كان مستخدم عادي، account_id هو user_id مباشرة
        //     $currentUserId = $account_id;
        //  }
        // else {
        //     // إذا كان موظف، لا يمكنه الوصول لهذه الصفحة (صفحة خاصة بالمستخدمين فقط)
        //     return redirect()->to('/dashboard')->with('error', 'هذه الصفحة مخصصة للمستخدمين فقط');
        // }

        // if (!$currentUserId) {
        //     return redirect()->to('/login')->with('error', 'خطأ في جلسة المستخدم');
        // }

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
     * عرض تفاصيل طلب محدد
     */
    // public function showOrder($order_id)
    // {
    //     $this->checkAuth(); // تحقق من تسجيل الدخول

    //     $itemOrderModel = new ItemOrderModel();
    //     $order = $itemOrderModel->find($order_id);

    //     if (!$order) {
    //         return redirect()->back()->with('error', 'الطلب غير موجود');
    //     }

    //     $data['order'] = $order;
    //     return view('warehouse/showOrderView', $data); // فيو تفصيلي للطلب
    // }



    /**
     * صفحة  userView2
     */

    // public function userView2(): string
    // {
    //     $this->checkAuth(); // تحقق من تسجيل الدخول

    //     return view('user/userView2');
    // }



    /**
     * جلب تفاصيل العهدة  
     */
    public function getTransferDetails($transferId)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً'
            ]);
        }

        $transferModel = new TransferItemsModel();

        $transfer = $transferModel
            ->select(
                'transfer_items.*,
                 item_order.item_id,
                 item_order.brand,
                 item_order.model_num,
                 item_order.asset_num,
                 item_order.serial_num,
                 item_order.quantity,
                 item_order.note as item_note,
                 items.name as item_name,
                 from_user.name AS from_user_name,
                 from_user.user_dept AS from_user_dept,
                 from_user.user_ext AS from_user_ext,
                 from_user.email AS from_user_email,
                 usage_status.usage_status AS usage_status_name,
                 order_status.status AS order_status_name'
            )
            ->join('item_order', 'item_order.item_order_id = transfer_items.item_order_id', 'left')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->join('users AS from_user', 'from_user.user_id = transfer_items.from_user_id', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
            ->join('order_status', 'order_status.id = transfer_items.order_status_id', 'left')
            ->where('transfer_items.transfer_item_id', $transferId)
            ->first();

        if (!$transfer) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'الطلب غير موجود'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $transfer
        ]);
    }



    /**
     * قبول أو رفض طلب العهدة
     */
    public function respondToTransfer()
    {
        $this->response->setContentType('application/json');

        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً'
            ]);
        }

        $json = $this->request->getJSON();

        if (!$json) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'بيانات غير صحيحة'
            ]);
        }

        $transferId = $json->transfer_id ?? null;
        $action = $json->action ?? null; // 'accept' or 'reject'

        if (!$transferId || !$action) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'البيانات المطلوبة غير مكتملة'
            ]);
        }

        try {
            $transferModel = new TransferItemsModel();

            // جلب معلومات الطلب
            $transfer = $transferModel->find($transferId);

            if (!$transfer) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'الطلب غير موجود'
                ]);
            }

            // التحقق من أن المستخدم الحالي هو المستقبل
            $currentUserId = session()->get('employee_id');
            if ($transfer->to_user_id !== $currentUserId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ليس لديك صلاحية لهذا الإجراء'
                ]);
            }

            // التحقق من أن الطلب في حالة "قيد الانتظار"
            if ($transfer->order_status_id != 1) {
                $statusText = $transfer->order_status_id == 2 ? 'مقبول' : 'مرفوض';
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'هذا الطلب ' . $statusText . ' مسبقاً'
                ]);
            }

            // تحديد حالة الطلب: 2 = مقبول, 3 = مرفوض
            $orderStatusId = ($action === 'accept') ? 2 : 3;

            // تحديث حالة الطلب
            $updated = $transferModel->update($transferId, [
                'order_status_id' => $orderStatusId,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if (!$updated) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل تحديث حالة الطلب'
                ]);
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

        // استقبال transfer_id من POST body بدلاً من URL
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


    /**
     * عرض العهد الخاصة بالمستخدم الحالي (من order + transfer_items)
     */
    public function userView2(): string
    {
        $this->checkAuth();

        // التحقق من تسجيل الدخول
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        // تحديد نوع الحساب والمستخدم الحالي
        $isEmployee = session()->get('isEmployee');
        $account_id = session()->get('employee_id');
        $currentUserId = null;
        // problem here
        if (!$isEmployee) {
            // مستخدم عادي
            $currentUserId = $account_id;
        } else {
            // موظف لا يدخل هنا
            return redirect()->to('/dashboard')->with('error', 'هذه الصفحة مخصصة للمستخدمين فقط');
        }

        // حماية إضافية
        if (!$currentUserId) {
            return redirect()->to('/login')->with('error', 'خطأ في جلسة المستخدم');
        }

        //  1. جلب العهد من جدول transfer_items
        $transferItemsModel = new \App\Models\TransferItemsModel();

        $transferItems = $transferItemsModel
            ->distinct()
            ->select(
                'transfer_items.transfer_item_id AS id,
             transfer_items.created_at,
             transfer_items.item_order_id,
             transfer_items.is_opened, 
             item_order.asset_num,
             item_order.serial_num,
             from_user.name AS from_user_name,
             from_user.user_dept AS from_user_dept,
             usage_status.usage_status AS usage_status_name,
             order_status.status AS order_status_name,
             "transfer_items" AS source_table'
            )
            ->join('item_order', 'item_order.item_order_id = transfer_items.item_order_id', 'left')
            ->join('users AS from_user', 'from_user.user_id = transfer_items.from_user_id', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
            ->join('order_status', 'order_status.id = transfer_items.order_status_id', 'left')
            ->where('transfer_items.to_user_id', $currentUserId)
            ->where('item_order.usage_status_id !=', 2)
            ->orderBy('transfer_items.created_at', 'DESC')
            ->findAll();

        //  2. جلب العهد من جدول order
        $orderModel = new \App\Models\OrderModel();

        $orders = $orderModel
            ->distinct()
            ->select(
                'order.order_id AS id,
             order.created_at,
             order.to_user_id,
             order_status.status AS order_status_name,
             usage_status.usage_status AS usage_status_name,
             from_user.name AS from_user_name,
             from_user.user_dept AS from_user_dept,
             item_order.asset_num,
             item_order.serial_num,
             "orders" AS source_table'
            )
            ->join('item_order', 'item_order.order_id = order.order_id', 'left')
            ->join('users AS from_user', 'from_user.user_id = order.from_user_id', 'left') // JOIN مع users
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
            ->join('order_status', 'order_status.id = order.order_status_id', 'left')
            ->where('order.to_user_id', $currentUserId)
            ->where('item_order.usage_status_id !=', 2)
            ->orderBy('order.created_at', 'DESC')
            ->findAll();

        //  3. دمج النتائج
        $allOrders = array_merge($orders, $transferItems);

        //  4. تمرير البيانات للواجهة
        return view('user/userView2', [
            'orders' => $allOrders
        ]);
    }
}
