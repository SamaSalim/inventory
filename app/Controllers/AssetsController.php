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
    UsageStatusModel,
    TransferItemsModel,
};
use App\Exceptions\AuthenticationException;

class AssetsController extends BaseController
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
    protected $transferItemsModel;

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
        $this->transferItemsModel = new TransferItemsModel();
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $itemOrderModel = new \App\Models\ItemOrderModel();
        $roomModel      = new \App\Models\RoomModel();

        $search        = $this->request->getVar('search'); 
        $category      = $this->request->getVar('category');     
        $itemType      = $this->request->getVar('item_type');   
        $serialNumber  = $this->request->getVar('serial_number'); 
        $employeeId    = $this->request->getVar('employee_id');  
        $location      = $this->request->getVar('location');     

        $builder = $itemOrderModel
            ->distinct()
            ->select('
                item_order.order_id, 
                item_order.created_at, 
                item_order.created_by, 
                item_order.room_id,
                employee.name AS created_by_name, 
                employee.emp_id AS employee_id, 
                employee.emp_ext AS extension,
                items.name AS item_name,
                minor_category.name AS category_name
            ')
            ->join('employee', 'employee.emp_id = item_order.created_by', 'left')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->orderBy('item_order.created_at', 'DESC')
            ->groupBy('item_order.order_id');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('item_order.order_id', $search)
                ->orLike('employee.name', $search)
                ->orLike('employee.emp_id', $search)
                ->orLike('employee.emp_ext', $search)
                ->orLike('items.name', $search)
                ->orLike('minor_category.name', $search)
                ->orLike('item_order.serial_num', $search)
                ->groupEnd();
        }

        $builder->whereIn('item_order.order_id', function($sub) {
        return $sub->select('order_id')
               ->from('item_order')
               ->whereNotIn('usage_status_id', [2, 4]); 
        });
        
        if (!empty($itemType)) {
            $builder->like('items.name', $itemType);
        }

        if (!empty($category)) {
            $builder->where('minor_category.id', $category);
        }

        if (!empty($serialNumber)) {
            $builder->like('item_order.serial_num', $serialNumber);
        }

        if (!empty($employeeId)) {
            $builder->where('employee.emp_id', $employeeId);
        }

        if (!empty($location)) {
            $builder
                ->join('room', 'room.id = item_order.room_id', 'left')
                ->join('section', 'section.id = room.section_id', 'left')
                ->join('floor', 'floor.id = section.floor_id', 'left')
                ->join('building', 'building.id = floor.building_id', 'left')
                ->groupStart()
                    ->like('room.code', $location)
                    ->orLike('section.code', $location)
                    ->orLike('floor.code', $location)
                    ->orLike('building.code', $location)
                ->groupEnd();
        }

        $itemOrders = $builder->paginate(10, 'orders');
        $pager = $itemOrderModel->pager;

        foreach ($itemOrders as $order) {
            $order->location_code = $roomModel->getFullLocationCode($order->room_id);
        }

        $minorCategoryModel = new \App\Models\MinorCategoryModel();
        $categories = $minorCategoryModel->select('minor_category.*, major_category.name AS major_category_name')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->findAll();

        $stats = $this->getWarehouseStats();
        $statuses = (new \App\Models\OrderStatusModel())->findAll();
        $usageStatuses = (new \App\Models\UsageStatusModel())->findAll();

        return view('assets/assets_view', [
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
            ]
        ]);
    }

    // InventoryController.php

    private function getWarehouseStats(): array
    {
        // إجمالي الكميات المدخلة للنظام (المخزون التاريخي)
        $totalQuantityResult = $this->itemOrderModel->selectSum('quantity')->first();
        $totalReceipts = $totalQuantityResult ? (int)$totalQuantityResult->quantity : 0;
        
        // 1. الأصناف المتوفرة (الأصناف التي لم يتم تعميدها بعد)
        $availableItems = $this->itemOrderModel
            ->join('order', 'order.order_id = item_order.order_id', 'left')
            // استبعاد الأصناف التي حالة الطلب الرئيسي لها 'مقبول' (2)
            ->where('order.order_status_id !=', 2) 
            ->countAllResults();
        
        // 2. عدد الإدخالات (الأصناف المُعمّدة/المحولة بنجاح)
        $totalEntries = $this->itemOrderModel
            ->join('order', 'order.order_id = item_order.order_id')
            ->where('order.order_status_id', 2) // مقبول
            ->countAllResults();
        
        // 3. عدد أصناف الرجيع
        $returnedItemsCount = $this->itemOrderModel
            ->where('usage_status_id', 2) // 2 = مرجع
            ->countAllResults();
        
        // 4. التصنيف الأكثر شيوعًا
        $topCategoryResult = $this->itemOrderModel
            ->select('items.minor_category_id, minor_category.name, COUNT(*) as count')
            ->join('items', 'items.id = item_order.item_id')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->groupBy('items.minor_category_id')
            ->orderBy('count', 'DESC')
            ->first();
        
        $topCategory = $topCategoryResult ? $topCategoryResult->name : 'غير محدد';
        
        // 5. آخر إدخال
        $lastEntry = $this->itemOrderModel
            ->select('item_order.created_at, items.name')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->orderBy('item_order.created_at', 'DESC')
            ->first();
        
        return [
            'total_receipts' => $totalReceipts,
            'available_items' => $availableItems, 
            'total_entries' => $totalEntries, 
            'returned_items' => $returnedItemsCount,
            'top_category' => $topCategory,
            'last_entry' => $lastEntry ? [
                'item' => $lastEntry->name ?? 'غير محدد', 
                'date' => date('Y-m-d H:i', strtotime($lastEntry->created_at))
            ] : null
        ];
    }

public function orderDetails($id)
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
    $historyModel       = new \App\Models\HistoryModel(); // ✅ add this

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
    ->whereNotIn('usage_status_id', [2, 4])
    ->findAll();

    foreach ($items as $item) {
        $itemData = $itemModel->find($item->item_id);
        $minor    = $itemData ? $minorCatModel->find($itemData->minor_category_id) : null;
        $major    = $minor ? $majorCatModel->find($minor->major_category_id) : null;

     
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

        $item->item_name            = $itemData->name ?? 'غير معروف';
        $item->minor_category_name  = $minor->name ?? 'غير معروف';
        $item->major_category_name  = $major->name ?? 'غير معروف';
        $item->location_code        = $roomModel->getFullLocationCode($item->room_id);
        $item->created_by_name      = $employeeModel->where('emp_id', $item->created_by)->first()->name ?? 'غير معروف';
    }

    return view('assets/return_order', [
        'order'       => $order,
        'items'       => $items,
        'item_count'  => count($items),
    ]);
}

// transferView - عرض صفحة تحويل العهدة
public function transferView($orderId = null)
{
        if (!session()->get('isLoggedIn')) {
            throw new AuthenticationException();
        }

    $itemOrderModel = new \App\Models\ItemOrderModel();
    
    $itemOrderId = null;
    $actualOrderId = null;
    
    // دعم 3 طرق لاستقبال البيانات
    if ($orderId !== null) {
        $itemOrder = $itemOrderModel->where('order_id', $orderId)->first();
        if ($itemOrder) {
            $itemOrderId = $itemOrder->item_order_id;
            $actualOrderId = $orderId;
        } else {
            $itemOrderId = $orderId;
        }
    } elseif ($this->request->getGet('item_order_id')) {
        $itemOrderId = $this->request->getGet('item_order_id');
    } elseif ($this->request->getGet('order_id')) {
        $actualOrderId = $this->request->getGet('order_id');
        $itemOrder = $itemOrderModel->where('order_id', $actualOrderId)->first();
        if ($itemOrder) {
            $itemOrderId = $itemOrder->item_order_id;
        }
    }
    
    if (!$itemOrderId) {
        return redirect()->back()->with('error', 'معرف العنصر مطلوب');
    }

    $itemModel = new \App\Models\ItemModel();
    $minorCatModel = new \App\Models\MinorCategoryModel();
    $majorCatModel = new \App\Models\MajorCategoryModel();
    $usageStatusModel = new \App\Models\UsageStatusModel();
    $userModel = new \App\Models\UserModel();
    $orderModel = new \App\Models\OrderModel();
    $transferItemsModel = new \App\Models\TransferItemsModel();
    
    $currentUserId = session()->get('isEmployee') ? session()->get('employee_id') : session()->get('user_id');
    
    // جلب الـ item_order
    $itemOrder = $itemOrderModel->find($itemOrderId);
    
    if (!$itemOrder) {
        throw new \Exception('العهدة غير موجودة');
    }
    
    // استخدام order_id من الـ item_order إذا لم يكن موجود
    if (!$actualOrderId) {
        $actualOrderId = $itemOrder->order_id;
    }
    
    $order = null;
    $currentUser = null;
    $actualOwnerId = null;

    // البحث عن صاحب العهدة الحالي
    $transferItem = $transferItemsModel
        ->where('item_order_id', $itemOrderId)
        ->where('order_status_id', 2)
        ->orderBy('created_at', 'DESC')
        ->first();
    
    if ($transferItem) {
        $actualOwnerId = $transferItem->to_user_id;
        $currentUser = $userModel->where('user_id', $actualOwnerId)->first();
    }
    
    if (!$currentUser && $actualOrderId) {
        $order = $orderModel->find($actualOrderId);
        
        if ($order && $order->to_user_id) {
            $actualOwnerId = $order->to_user_id;
            $currentUser = $userModel->where('user_id', $actualOwnerId)->first();
        }
    }
    
    if (!$currentUser) {
        throw new \Exception('لا يمكن تحديد صاحب العهدة الحالي');
    }

    // التحقق من الصلاحية
    $isOwner = ($actualOwnerId == $currentUserId);
    $canTransferOthers = canTransfer();
    
    if (!$isOwner && !$canTransferOthers) {
        return redirect()->back()->with('error', 'ليس لديك صلاحية تحويل هذه العهدة');
    }

    // ✅ جلب كل الأصناف المتاحة للتحويل من نفس الطلب
    $items = $itemOrderModel
        ->where('order_id', $actualOrderId)
        ->whereNotIn('usage_status_id', [2, 3])
        ->findAll();

    // ✅ إزالة شرط منع الدخول وإضافة متغير لحالة الأصناف
    $hasAvailableItems = !empty($items);

    // ✅ معالجة بيانات الأصناف فقط إذا كانت موجودة
    if ($hasAvailableItems) {
        foreach ($items as $item) {
            $itemData = $itemModel->find($item->item_id);
            $minor = $itemData ? $minorCatModel->find($itemData->minor_category_id) : null;
            $major = $minor ? $majorCatModel->find($minor->major_category_id) : null;

            $item->item_name = $itemData->name ?? 'غير معروف';
            $item->minor_category_name = $minor->name ?? 'غير معروف';
            $item->major_category_name = $major->name ?? 'غير معروف';
            $item->usage_status_name = $usageStatusModel->find($item->usage_status_id)->usage_status ?? 'غير معروف';
        }
    }

    // جلب جميع المستخدمين ماعدا صاحب العهدة الحالي
    $users = $userModel->where('user_id !=', $actualOwnerId)->findAll();

    return view('assets/transfer_order', [
        'items' => $items, // ✅ سيكون array فارغ إذا لم توجد أصناف
        'users' => $users,
        'order_id' => $actualOrderId,
        'order' => $order,
        'current_user' => $currentUser,
        'has_available_items' => $hasAvailableItems // ✅ متغير جديد للتحكم في العرض
    ]);
}

//  processTransfer - معالجة طلب التحويل
public function processTransfer()
{
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

    $itemOrderIds = $json->items ?? [];
    $fromUserId = $json->from_user_id ?? null;
    $toUserId = $json->to_user_id ?? null;
    $note = $json->note ?? '';

    // التحقق من البيانات
    if (empty($itemOrderIds) || !$fromUserId || !$toUserId) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'جميع الحقول مطلوبة'
        ]);
    }

    if ($fromUserId === $toUserId) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'لا يمكن التحويل لنفس الشخص'
        ]);
    }

    try {
        $itemOrderModel = new \App\Models\ItemOrderModel();
        $transferItemsModel = new \App\Models\TransferItemsModel();
        $userModel = new \App\Models\UserModel();

        // Get user details for email
        $fromUser = $userModel->where('user_id', $fromUserId)->first();
        $toUser = $userModel->where('user_id', $toUserId)->first();

        if (!$fromUser || !$toUser) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'المستخدم غير موجود'
            ]);
        }

        // Get items details for email
        $itemsDetails = [];
        $firstItemOrderId = null;
        
        // ✅ معالجة كل صنف بشكل منفصل حسب item_order_id
        foreach ($itemOrderIds as $itemOrderId) {
            if ($firstItemOrderId === null) {
                $firstItemOrderId = $itemOrderId;
            }
            
            // ✅ جلب الصنف المحدد بناءً على item_order_id
            $currentItem = $itemOrderModel
                ->select('item_order.*, items.name as item_name')
                ->join('items', 'items.id = item_order.item_id')
                ->find($itemOrderId); // ✅ البحث بـ item_order_id وليس order_id
            
            if (!$currentItem) {
                log_message('warning', "Item order {$itemOrderId} not found");
                continue;
            }

            // ✅ التحقق من حالة هذا الصنف بالتحديد (item_order_id)
            // 1 = جديد, 4 = معاد صرفة (يمكن تحويلهم)
            // 2 = رجيع, 3 = تحويل (لا يمكن تحويلهم)
            if (in_array($currentItem->usage_status_id, [2, 3])) {
                $statusName = $currentItem->usage_status_id == 2 ? 'رجيع' : 'قيد التحويل';
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "الصنف '{$currentItem->item_name}' في حالة '{$statusName}' ولا يمكن تحويله"
                ]);
            }


            // ✅ التحقق من عدم وجود طلب تحويل قيد الانتظار لهذا الصنف بالتحديد
            // $existingTransfer = $transferItemsModel
            //     ->where('item_order_id', $itemOrderId) // ✅ التحقق من item_order_id المحدد
            //     ->where('order_status_id', 1) // قيد الانتظار
            //     ->first();
            
            // if ($existingTransfer) {
            //     return $this->response->setJSON([
            //         'success' => false,
            //         'message' => "الصنف '{$currentItem->item_name}' لديه طلب تحويل قيد الانتظار بالفعل"
            //     ]);
            // }

            $itemsDetails[] = $currentItem;

            // ✅ تحديث حالة هذا الصنف بالتحديد إلى "تحويل" (3)
            $itemOrderModel->update($itemOrderId, [ // ✅ تحديث item_order_id المحدد
                'usage_status_id' => 3, // تحويل
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // عند قبول التحويل (order_status_id = 2)
            // $itemOrderModel->update($itemOrderId, [
            //     'usage_status_id' => 5, // ✅تتغير الحالة إلى " مستعمل" عند قبول التحويل
            //     'updated_at' => date('Y-m-d H:i:s')
            // ]);

            // ✅ إضافة سجل في جدول transfer_items لهذا الصنف
            $transferData = [
                'item_order_id' => $itemOrderId, // ✅ ربط بـ item_order_id المحدد
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
                'order_status_id' => 1, // قيد الانتظار
                'note' => $note,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $transferItemsModel->insert($transferData);
        }

        // Send email notification
        if (!empty($itemsDetails)) {
            $this->sendTransferEmail($toUser, $fromUser, $itemsDetails, $note, $firstItemOrderId);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'تم إنشاء طلب التحويل بنجاح'
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Transfer Error: ' . $e->getMessage());
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'حدث خطأ: ' . $e->getMessage()
        ]);
    }
}


private function sendTransferEmail($toUser, $fromUser, $itemsDetails, $note, $orderId)
{
    try {
        $email = \Config\Services::email();

        $email->setTo($toUser->email);
        $email->setSubject('إشعار تحويل أصول جديد - KAMC Inventory System');

        $itemsList = '';
        foreach ($itemsDetails as $item) {
            $itemsList .= "
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$item->item_name}</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$item->asset_num}</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$item->serial_num}</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>" . ($item->brand ?? 'N/A') . "</td>
                </tr>
            ";
        }

        $transferUrl = "http://localhost/inventory/AssetsController/transferView/{$orderId}";

        $message = "
            <html dir='rtl'>
            <head>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        direction: rtl; 
                        background-color: #f5f5f5;
                        margin: 0;
                        padding: 0;
                    }
                    .container { 
                        max-width: 600px; 
                        margin: 20px auto; 
                        background-color: white;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    }
                    .header { 
                        background-color:  #0896baff; 
                        color: white; 
                        padding: 30px 15px; 
                        text-align: center;
                        font-size: 28px;
                        font-weight: bold;
                    }
                    .content { 
                        padding: 30px 20px; 
                        background-color: #f9f9f9; 
                    }
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin: 15px 0; 
                        background-color: white;
                    }
                    th { 
                        background-color: #0896baff; 
                        color: white; 
                        padding: 12px 10px; 
                        text-align: right;
                        font-weight: bold;
                    }
                    td { 
                        padding: 10px; 
                        border: 1px solid #ddd; 
                    }
                    .note { 
                        background-color: #fff3cd; 
                        padding: 15px; 
                        border-right: 4px solid #ffc107; 
                        margin: 15px 0; 
                    }
                    .info-row {
                        margin: 10px 0;
                        line-height: 1.6;
                    }
                    .btn-container {
                        text-align: center;
                        margin: 30px 0;
                    }
                    .btn {
                        display: inline-block;
                        background-color: #0896baff;
                        color: white !important;
                        padding: 15px 40px;
                        text-decoration: none;
                        border-radius: 5px;
                        font-weight: bold;
                        font-size: 16px;
                    }
                    .btn:hover {
                        background-color: #0896baff;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        إشعار تحويل أصول
                    </div>
                    <div class='content'>
                        <p class='info-row'>,عزيزي/عزيزتي <strong>{$toUser->name}</strong></p>
                        <p class='info-row'>تم إرسال طلب تحويل أصول إليك من قبل: <strong>{$fromUser->name}</strong></p>
                        <p class='info-row'><strong>رقم الطلب:</strong> {$orderId}</p>
                        
                        
                        <h3 style='color: #0896baff; margin-top: 25px;'>تفاصيل الأصول المحولة:</h3>
                        <table>
                            <tr>
                                <th>اسم الصنف</th>
                                <th>رقم الأصل</th>
                                <th>الرقم التسلسلي</th>
                                <th>العلامة التجارية</th>
                            </tr>
                            {$itemsList}
                        </table>
                        
                        <div class='btn-container'>
                            <a href='{$transferUrl}' class='btn'>مراجعة النظام لقبول أو رفض الطلب</a>
                        </div>
                        
                        <p style='text-align: center; margin-top: 30px; color: #666;'>
                            شكراً لك،<br>
                            <strong>KAMC - نظام إدارة العهد</strong>
                        </p>
                    </div>
                </div>
            </body>
            </html>
        ";

        $email->setMessage($message);

        if ($email->send()) {
            log_message('info', "Transfer email sent successfully to {$toUser->email}");
            return true;
        } else {
            log_message('error', 'Email sending failed: ' . $email->printDebugger(['headers']));
            return false;
        }
    } catch (\Exception $e) {
        log_message('error', 'Email Error: ' . $e->getMessage());
        return false;
    }
}

// public function showTransfer($orderId)
// {
//     $transferItemsModel = new \App\Models\TransferItemsModel();
//     $itemOrderModel     = new \App\Models\ItemOrderModel();
//     $userModel          = new \App\Models\UserModel();
//     $itemModel          = new \App\Models\ItemModel();
//     $minorCatModel      = new \App\Models\MinorCategoryModel();
//     $majorCatModel      = new \App\Models\MajorCategoryModel();
//     $roomModel          = new \App\Models\RoomModel();
//     $usageStatusModel   = new \App\Models\UsageStatusModel();

//     // جلب كل item_order_id للطلب
//     $itemOrders = $itemOrderModel->where('order_id', $orderId)->findAll();
    
//     if (empty($itemOrders)) {
//         return redirect()->back()->with('error', 'لا توجد عناصر لهذا الطلب');
//     }

//     // جلب كل item_order_id
//     $itemOrderIds = array_column($itemOrders, 'item_order_id');
    
//     // 🔥 التعديل الرئيسي: جلب آخر تحويل لكل صنف فقط
//     $transfers = [];
//     foreach ($itemOrderIds as $itemOrderId) {
//         $latestTransfer = $transferItemsModel
//             ->where('item_order_id', $itemOrderId)
//             ->orderBy('created_at', 'DESC')  // الأحدث أولاً
//             ->first();  // فقط أول نتيجة (الأحدث)
        
//         if ($latestTransfer) {
//             $transfers[] = $latestTransfer;
//         }
//     }

//     if (empty($transfers)) {
//         return redirect()->back()->with('error', 'لا توجد تحويلات لهذا الطلب');
//     }

//     // جلب معلومات المستخدمين من أول تحويل
//     $firstTransfer = $transfers[0];
//     $fromUser = $userModel->where('user_id', $firstTransfer->from_user_id)->first();
//     $toUser   = $userModel->where('user_id', $firstTransfer->to_user_id)->first();

//     $transferInfo = (object)[
//         'order_id'   => $orderId,
//         'from_name'  => $fromUser->name ?? 'غير معروف',
//         'to_name'    => $toUser->name ?? 'غير معروف',
//         'created_at' => $firstTransfer->created_at,
//     ];

    
//     // جلب تفاصيل كل الأصناف المحولة
//     $items = [];
//     foreach ($transfers as $transfer) {
//         $itemOrder = $itemOrderModel->find($transfer->item_order_id);
        
//         if (!$itemOrder) continue;

//         $itemData = $itemModel->find($itemOrder->item_id);
//         $minor    = $itemData ? $minorCatModel->find($itemData->minor_category_id) : null;
//         $major    = $minor ? $majorCatModel->find($minor->major_category_id) : null;
//         $usageStatus = $usageStatusModel->find($itemOrder->usage_status_id);

//         $items[] = (object)[
//             'transfer_item_id'     => $transfer->transfer_item_id,
//             'item_name'            => $itemData->name ?? 'غير معروف',
//             'minor_category_name'  => $minor->name ?? 'غير معروف',
//             'major_category_name'  => $major->name ?? 'غير معروف',
//             'model_num'            => $itemOrder->model_num,
//             'serial_num'           => $itemOrder->serial_num,
//             'asset_num'            => $itemOrder->asset_num,
//             'old_asset_num'        => $itemOrder->old_asset_num,
//             'brand'                => $itemOrder->brand,
//             'assets_type'          => $itemOrder->assets_type,
//             'location_code'        => $roomModel->getFullLocationCode($itemOrder->room_id),
//             'usage_status_name'    => $usageStatus->usage_status ?? 'غير معروف',
//             'note'                 => $transfer->note,
//             'created_at'           => $transfer->created_at,
//             'updated_at'           => $transfer->updated_at,
//         ];
//     }

//     return view('assets/show_transfer', [
//         'transfer'   => $transferInfo,
//         'items'      => $items,
//         'item_count' => count($items),
//     ]);
// }
}