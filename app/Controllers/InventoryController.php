<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{
    BuildingModel,
    EmployeeModel,
    FloorModel,
    ItemModel,
    ItemOrderModel,
    MajorCategoryModel,
    OrderModel,
    RoomModel,
    SectionModel,
    OrderStatusModel,
    UsageStatusModel
};
use CodeIgniter\HTTP\ResponseInterface;
use App\Exceptions\AuthenticationException;

class InventoryController extends BaseController
{
    protected $orderModel;
    protected $itemOrderModel;
    protected $employeeModel;
    protected $itemModel;
    protected $buildingModel;
    protected $floorModel;
    protected $sectionModel;
    protected $roomModel;
    protected $majorCategoryModel;
    protected $orderStatusModel;
    protected $usageStatusModel;
    protected $minorCategoryModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->itemOrderModel = new ItemOrderModel();
        $this->employeeModel = new EmployeeModel();
        $this->itemModel = new ItemModel();
        $this->buildingModel = new BuildingModel();
        $this->floorModel = new FloorModel();
        $this->sectionModel = new SectionModel();
        $this->roomModel = new RoomModel();
        $this->majorCategoryModel = new MajorCategoryModel();
        $this->orderStatusModel = new OrderStatusModel();
        $this->usageStatusModel = new UsageStatusModel();
        $this->minorCategoryModel = new \App\Models\MinorCategoryModel();
    }

    public function index()
    {
        //http://localhost/inventory88/UserController/dashboard
        // التحقق من تسجيل الدخول
        if (!session()->get('isLoggedIn')) {
            throw new AuthenticationException();
        }
        //  البيانات model
        $itemOrderModel = $this->itemOrderModel;
        $minorCategoryModel = $this->minorCategoryModel;
        $orderStatusModel = $this->orderStatusModel;
        $usageStatusModel = $this->usageStatusModel;

        // التقاط متغيرات البحث والفلاتر
        $search        = $this->request->getVar('search');
        $itemType      = $this->request->getVar('item_type');
        $category      = $this->request->getVar('category');
        $serialNumber  = $this->request->getVar('serial_number');
        $employeeId    = $this->request->getVar('employee_id');
        $assetNumber   = $this->request->getVar('asset_number');
        $location      = $this->request->getVar('location');
        $status        = $this->request->getVar('status');
        $usageStatus   = $this->request->getVar('usage_status');

        // بناء الاستعلام
        $builder = $itemOrderModel
            ->distinct()
            ->select('
                item_order.order_id,
                MAX(item_order.created_at) as created_at,
                MAX(item_order.created_by) as created_by,
                MAX(item_order.room_id) as room_id,
                MAX(item_order.asset_num) as asset_num,
                MAX(employee.name) AS created_by_name,
                MAX(employee.emp_id) AS created_by_emp_id, 
                MAX(employee.emp_ext) AS extension,
                MAX(items.name) AS item_name,
                MAX(minor_category.name) AS category_name,
                MAX(usage_status.usage_status) AS usage_status_name,
                COUNT(DISTINCT item_order.item_order_id) as items_count,
                MAX(users.name) as receiver_name,
                MAX(users.user_id) as receiver_employee_id,
                MAX(order_status.status) as order_status_name,
                
                MAX(CONCAT(building.code, "-", floor.code, "-", section.code, "-", room.code)) as full_location_code
            ')
            // الروابط
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->join('order', 'order.order_id = item_order.order_id', 'left')
            ->join('order_status', 'order_status.id = order.order_status_id', 'left')
            ->join('employee', 'employee.emp_id = order.from_user_id', 'left')
            ->join('users', 'users.user_id = order.to_user_id', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')

            ->join('room', 'room.id = item_order.room_id', 'left')
            ->join('section', 'section.id = room.section_id', 'left')
            ->join('floor', 'floor.id = section.floor_id', 'left')
            ->join('building', 'building.id = floor.building_id', 'left')

            ->groupBy('item_order.order_id')
            ->orderBy('MAX(item_order.created_at)', 'DESC');

        // -------------------------
        // شروط الفلترة المتقدمة 
        // -------------------------

        // 1. فلترة البحث العام
        if (!empty($search)) {
            $builder->groupStart()
                // إضافة البحث في عمود الموقع الكامل في البحث العام
                ->orLike('CONCAT(building.code, "-", floor.code, "-", section.code, "-", room.code)', $search)

                ->orLike('item_order.order_id', $search)
                ->orLike('employee.name', $search)
                ->orLike('employee.emp_id', $search)
                ->orLike('users.name', $search)
                ->orLike('users.user_id', $search)
                ->orLike('employee.emp_ext', $search)
                ->orLike('items.name', $search)
                ->orLike('minor_category.name', $search)
                ->orLike('item_order.serial_num', $search)
                ->orLike('item_order.asset_num', $search)
                ->groupEnd();
        }

        // 2. فلترة بنوع الصنف
        if (!empty($itemType)) {
            $builder->like('items.name', $itemType);
        }

        // 3. فلترة بالفئة الفرعية
        if (!empty($category)) {
            $builder->where('minor_category.id', $category);
        }

        // 4. فلترة برقم الأصول
        if (!empty($assetNumber)) {
            $builder->like('item_order.asset_num', $assetNumber);
        }

        // 5. فلترة بالرقم التسلسلي
        if (!empty($serialNumber)) {
            $builder->like('item_order.serial_num', $serialNumber);
        }

        // 6. فلترة بالرقم الوظيفي (شامل: المرسل أو المستلم)
        if (!empty($employeeId)) {
            $builder->groupStart()
                ->like('employee.emp_id', $employeeId)
                ->orLike('users.user_id', $employeeId)
                ->groupEnd();
        }

        // 7. فلترة بالموقع (الغرفة) - استخدام العمود المحسوب
        if (!empty($location)) {
            //  البحث المباشر في العمود المحسوب
            $builder->like('CONCAT(building.code, "-", floor.code, "-", section.code, "-", room.code)', $location);
        }

        // 8. فلترة بحالة الطلب (قبول/رفض)
        if (!empty($status)) {
            $builder->where('order_status.id', $status);
        }

        // 9. فلترة بحالة الاستخدام
        if (!empty($usageStatus)) {
            $builder->where('usage_status.id', $usageStatus);
        }

        // جلب البيانات وتقسيمها
    $itemOrders = $builder->paginate(10, 'orders');
    $pager = $itemOrderModel->pager;

    // Initialize history model to avoid undefined variable error
    $historyModel = new \App\Models\HistoryModel();

    foreach ($itemOrders as $order) {
        if (
            isset($order->usage_status_id) &&
            $order->usage_status_id == 1 &&
            !empty($order->asset_num)
        ) {
            $inHistory = $historyModel
                ->where('asset_num', $order->asset_num)
                ->where('usage_status_id', 1)
                ->first();

            if ($inHistory) {
                $order->usage_status_name = 'معاد صرفه';
            }
        }
    }


        // جلب البيانات المساعدة للعرض
        $categories = $minorCategoryModel->select('minor_category.*, major_category.name AS major_category_name')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->findAll();
        $stats = $this->getWarehouseStats();
        $statuses = $orderStatusModel->findAll();
        $usageStatuses = $usageStatusModel->findAll();

        return view('warehouse/warehouse_view', [
            'categories'     => $categories,
            'orders'         => $itemOrders,
            'stats'          => $stats,
            'statuses'       => $statuses,
            'usage_statuses' => $usageStatuses,
            'pager'          => $pager,
            'filters'        => [
                'search'        => $search,
                'category'      => $category,
                'item_type'     => $itemType,
                'serial_number' => $serialNumber,
                'employee_id'   => $employeeId,
                'location'      => $location,
                'asset_number'  => $assetNumber,
                'status'        => $status,
                'usage_status'  => $usageStatus,
            ]
        ]);
    }

    public function bulkDeleteOrders()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'طلب غير صحيح']);
        }

        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        try {
            $input = $this->request->getJSON(true);
            $orderIds = $input['order_ids'] ?? [];

            if (empty($orderIds)) {
                return $this->response->setJSON(['success' => false, 'message' => 'لم يتم اختيار أي طلبات']);
            }

            // تحميل المودلز
            $orderModel = new \App\Models\OrderModel();
            $itemOrderModel = new \App\Models\ItemOrderModel();

            $deletedCount = 0;

            foreach ($orderIds as $orderId) {
                if (is_numeric($orderId) && $orderId > 0) {
                    // التحقق من وجود الطلب
                    $order = $orderModel->find($orderId);

                    if ($order) {
                        // منع الحذف للطلبات المقبولة (2) أو قيد الانتظار (1)
                        if ($order->order_status_id == 2) {
                            $blockedOrders[] = "الطلب رقم {$orderId} (مقبول)";
                            continue;
                        }
                        if ($order->order_status_id == 1) {
                            $blockedOrders[] = "الطلب رقم {$orderId} (قيد الانتظار)";
                            continue;
                        }
                        // حذف عناصر الطلب المرتبطة أولاً (من جدول item_order)
                        $itemOrderModel->where('order_id', $orderId)->delete();

                        // ثم حذف الطلب نفسه (من جدول order)
                        if ($orderModel->delete($orderId)) {
                            $deletedCount++;
                        }
                    }
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "تم حذف {$deletedCount} طلب بنجاح"
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في حذف الطلبات: ' . $e->getMessage()
            ]);
        }
    }

    private function getWarehouseStats(): array
    {
        $totalQuantityResult = $this->itemOrderModel->selectSum('quantity')->first();
        $totalReceipts = $totalQuantityResult ? (int)$totalQuantityResult->quantity : 0;

        // $availableItems = $this->itemOrderModel->countAllResults();
        //  التعديل: عدد الأصناف المتوفرة = الإجمالي - المقبول (2)
        $availableItems = $this->itemOrderModel
            ->join('order', 'order.order_id = item_order.order_id', 'left')
            // استبعاد الأصناف التي حالة الطلب الرئيسي لها 'مقبول' (2)
            ->where('order.order_status_id !=', 2)
            ->countAllResults();

        // عدد الأصناف التي تم تعميدها (أي تمت الموافقة عليها) - يبقى كما تم تعديله
        $totalEntries = $this->itemOrderModel
            ->join('order', 'order.order_id = item_order.order_id')
            ->where('order.order_status_id', 2) // مقبول
            ->countAllResults();

        //  تغيير: عدد الأصناف المرجعة بدلاً من المخزون المنخفض
        // القديم:
        // $lowStock = $this->itemOrderModel->where('quantity <', 10)->where('quantity >', 0)->countAllResults();

        // الجديد:
        $returnedItemsCount = $this->itemOrderModel
            ->where('usage_status_id', 2) // 2 = مرجع
            ->countAllResults();

        $topCategoryResult = $this->itemOrderModel
            ->select('items.minor_category_id, minor_category.name, COUNT(*) as count')
            ->join('items', 'items.id = item_order.item_id')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->groupBy('items.minor_category_id')
            ->orderBy('count', 'DESC')
            ->first();

        $topCategory = $topCategoryResult ? $topCategoryResult->name : 'غير محدد';

        $lastEntry = $this->itemOrderModel
            ->select('item_order.created_at, items.name')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->orderBy('item_order.created_at', 'DESC')
            ->first();

        return [
            'total_receipts' => $totalReceipts,
            'available_items' => $availableItems,
            'total_entries' => $totalEntries,
            'returned_items' => $returnedItemsCount, // ✅ اسم جديد
            'top_category' => $topCategory,
            'last_entry' => $lastEntry ? [
                'item' => $lastEntry->name ?? 'غير محدد',
                'date' => date('Y-m-d H:i', strtotime($lastEntry->created_at))
            ] : null
        ];
    }


public function showOrder($id)
{
    $orderModel         = new \App\Models\OrderModel();
    $itemOrderModel     = new \App\Models\ItemOrderModel();
    $userModel          = new \App\Models\UserModel();
    $itemModel          = new \App\Models\ItemModel();
    $minorCatModel      = new \App\Models\MinorCategoryModel();
    $majorCatModel      = new \App\Models\MajorCategoryModel();
    $roomModel          = new \App\Models\RoomModel();
    $usageStatusModel   = new \App\Models\UsageStatusModel();
    $employeeModel      = new \App\Models\EmployeeModel();
    $statusModel        = new \App\Models\OrderStatusModel();
    $historyModel       = new \App\Models\HistoryModel();

    $order = $orderModel->find($id);

    if (!$order) {
        return redirect()->back()->with('error', 'الطلب غير موجود');
    }

    $fromUser = $userModel->where('user_id', $order->from_user_id)->first();
    $toUser   = $userModel->where('user_id', $order->to_user_id)->first();
    $status   = $statusModel->find($order->order_status_id);

    $order->from_name    = $fromUser->name ?? 'غير معروف';
    $order->to_name      = $toUser->name ?? 'غير معروف';
    $order->status_name  = $status->status ?? 'غير معروف';

    $items = $itemOrderModel
        ->where('order_id', $id)
        ->findAll();

    foreach ($items as $item) {
        $itemData = $itemModel->find($item->item_id);
        $minor    = $itemData ? $minorCatModel->find($itemData->minor_category_id) : null;
        $major    = $minor ? $majorCatModel->find($minor->major_category_id) : null;

        // ✅ Detect "معاد صرفه": usage_status_id = 1 + has return history
        if ($item->usage_status_id == 1) {
            $hasReturnHistory = $historyModel
                ->where('item_order_id', $item->item_order_id)
                ->where('usage_status_id', 2)
                ->first();

            if ($hasReturnHistory) {
                $item->usage_status_name = 'معاد صرفه';
            } else {
                $item->usage_status_name = $usageStatusModel->find($item->usage_status_id)->usage_status ?? 'غير معروف';
            }
        } else {
            $item->usage_status_name = $usageStatusModel->find($item->usage_status_id)->usage_status ?? 'غير معروف';
        }

        // ✅ Support both employee and user creators
        $creator = $employeeModel->where('emp_id', $item->created_by)->first();
        if (!$creator) {
            $creator = $userModel->where('user_id', $item->created_by)->first();
        }
        $item->created_by_name = $creator->name ?? 'غير معروف';

        $item->item_name            = $itemData->name ?? 'غير معروف';
        $item->minor_category_name = $minor->name ?? 'غير معروف';
        $item->major_category_name = $major->name ?? 'غير معروف';
        $item->location_code       = $roomModel->getFullLocationCode($item->room_id);
    }

    return view('warehouse/show_order', [
        'order'      => $order,
        'items'      => $items,
        'item_count' => count($items),
    ]);
}


    public function deleteOrder($orderId)
    {
        try {
            if (!$orderId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'رقم الطلب غير محدد.'
                ]);
            }

            // التحقق من وجود الطلب
            $order = $this->orderModel->find($orderId);
            if (!$order) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'الطلب غير موجود.'
                ]);
            }
            //  الإضافة الجديدة: منع الحذف إذا كانت حالة الطلب مقبولة  
            if ($order->order_status_id == 2) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'لا يمكن حذف الطلب بعد قبوله وتعميده.'
                ]);
            }
            if ($order->order_status_id == 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'لا يمكن حذف الطلب وهو قيد الانتظار.'
                ]);
            }
            // التحقق من الصلاحيات (اختياري)
            $currentUserId = session()->get('employee_id');
            if ($order->from_employee_id !== $currentUserId) {
                // يمكن إضافة فحص للأدمن هنا
                // return $this->response->setJSON([
                //     'success' => false,
                //     'message' => 'ليس لديك صلاحية لحذف هذا الطلب.'
                // ]);
            }


            // عد العناصر قبل الحذف
            $itemsCount = $this->itemOrderModel->where('order_id', $orderId)->countAllResults();

            // حذف الطلب (سيحذف العناصر تلقائياً بسبب CASCADE في قاعدة البيانات)
            $deleteResult = $this->orderModel->delete($orderId);

            if (!$deleteResult) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل في حذف الطلب.'
                ]);
            }

            log_message('info', "تم حذف الطلب {$orderId} مع {$itemsCount} عنصر بواسطة المستخدم {$currentUserId}");

            return $this->response->setJSON([
                'success' => true,
                'message' => "تم حذف الطلب رقم {$orderId} مع جميع عناصره ({$itemsCount} عنصر) بنجاح."
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in deleteOrder: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في حذف الطلب: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * حذف عنصر واحد من الطلب
     */
    public function deleteOrderItem($itemOrderId)
    {
        try {
            if (!$itemOrderId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'رقم العنصر غير محدد.'
                ]);
            }

            // التحقق من وجود العنصر
            $orderItem = $this->itemOrderModel->find($itemOrderId);
            if (!$orderItem) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'العنصر غير موجود.'
                ]);
            }

            // الحصول على معلومات الطلب للتحقق من الصلاحيات
            $order = $this->orderModel->find($orderItem->order_id);
            if (!$order) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'الطلب المرتبط بهذا العنصر غير موجود.'
                ]);
            }

            // التحقق من الصلاحيات (اختياري)
            $currentUserId = session()->get('employee_id');

            // فحص عدد العناصر المتبقية في الطلب
            $remainingItemsCount = $this->itemOrderModel->where('order_id', $orderItem->order_id)->countAllResults();

            if ($remainingItemsCount <= 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'لا يمكن حذف العنصر الأخير من الطلب. احذف الطلب كاملاً بدلاً من ذلك.'
                ]);
            }

            // الحصول على معلومات العنصر للـ log
            $itemInfo = $this->itemOrderModel
                ->select('item_order.*, items.name as item_name')
                ->join('items', 'items.id = item_order.item_id')
                ->where('item_order.item_order_id', $itemOrderId)
                ->first();

            // حذف العنصر
            $deleteResult = $this->itemOrderModel->delete($itemOrderId);

            if (!$deleteResult) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل في حذف العنصر.'
                ]);
            }

            $itemName = $itemInfo->item_name ?? 'غير محدد';
            $assetNum = $itemInfo->asset_num ?? '';

            log_message('info', "تم حذف العنصر {$itemName} (رقم الأصول: {$assetNum}) من الطلب {$orderItem->order_id} بواسطة المستخدم {$currentUserId}");

            return $this->response->setJSON([
                'success' => true,
                'message' => "تم حذف العنصر ({$itemName}) بنجاح.",
                'order_id' => $orderItem->order_id,
                'remaining_items' => $remainingItemsCount - 1
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in deleteOrderItem: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في حذف العنصر: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * الحصول على تفاصيل الطلب مع العناصر (لتحديث الواجهة بعد الحذف)
     */
    public function getOrderDetails($orderId)
    {
        try {
            if (!$orderId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'رقم الطلب غير محدد.'
                ]);
            }

            $order = $this->orderModel->find($orderId);
            if (!$order) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'الطلب غير موجود.'
                ]);
            }

            $orderItems = $this->itemOrderModel
                ->select('item_order.*, items.name as item_name')
                ->join('items', 'items.id = item_order.item_id')
                ->where('order_id', $orderId)
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'order' => $order,
                    'items' => $orderItems,
                    'items_count' => count($orderItems)
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getOrderDetails: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في جلب تفاصيل الطلب: ' . $e->getMessage()
            ]);
        }
    }

    //  تحديث حالة الطلب (مقبول أو مرفوض)
    public function updateOrderStatus($orderId)
    {
        $orderModel = new \App\Models\OrderModel();

        // قراءة البيانات القادمة من الجافاسكربت
        $data = json_decode($this->request->getBody(), true);
        $status_id = $data['status_id'] ?? null;

        if (!$status_id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'لم يتم تحديد الحالة.']);
        }

        // التحقق من وجود الطلب
        $order = $orderModel->find($orderId);
        if (!$order) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'الطلب غير موجود.']);
        }

        // تحديث الحالة
        $orderModel->update($orderId, ['order_status_id' => $status_id]);

        return $this->response->setJSON(['success' => true, 'message' => 'تم تحديث حالة الطلب بنجاح.']);
    }
}
