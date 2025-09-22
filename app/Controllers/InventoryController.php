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
        // exception handling
        if (! session()->get('isLoggedIn')) {
            throw new AuthenticationException();
        }

        $itemOrderModel = new \App\Models\ItemOrderModel();

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
            ->groupBy('item_order.order_id') // Prevent duplicate rows
            ->orderBy('item_order.created_at', 'DESC')
            ->findAll();

        // Other data
        $minorCategoryModel = new \App\Models\MinorCategoryModel();
        $categories = $minorCategoryModel->select('minor_category.*, major_category.name AS major_category_name')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->findAll();

        $stats = $this->getWarehouseStats();

        $orderStatusModel = new \App\Models\OrderStatusModel();
        $statuses = $orderStatusModel->findAll();

        $usageStatusModel = new \App\Models\UsageStatusModel();
        $usageStatuses = $usageStatusModel->findAll();

        return view('warehouse/warehouseView', [
            'categories' => $categories,
            'orders' => $itemOrders,
            'stats' => $stats,
            'statuses' => $statuses,
            'usage_statuses' => $usageStatuses,
        ]);
    }

    public function store()
    {
        try {
            // إنشاء الطلب الرئيسي
            $orderData = [
                'from_employee_id' => $this->request->getPost('from_employee_id'),
                'to_employee_id' => $this->request->getPost('to_employee_id'),
                'order_status_id' => 1,
                'note' => $this->request->getPost('notes') ?? ''
            ];

            $orderId = $this->orderModel->insert($orderData);

            // العثور على الصنف
            $itemName = $this->request->getPost('item');
            $item = $this->itemModel->where('name', $itemName)->first();

            $quantity = (int)$this->request->getPost('quantity');
            $assetNumbers = [];
            $serialNumbers = [];

            for ($i = 1; $i <= $quantity; $i++) {
                $assetNum = trim($this->request->getPost("asset_num_{$i}"));
                $serialNum = trim($this->request->getPost("serial_num_{$i}"));

                // تحقق من قاعدة البيانات
                if ($this->itemOrderModel->where('asset_num', $assetNum)->first()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "رقم الأصول {$assetNum} موجود مسبقاً"
                    ]);
                }
                if ($this->itemOrderModel->where('serial_num', $serialNum)->first()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الرقم التسلسلي {$serialNum} موجود مسبقاً"
                    ]);
                }

                $assetNumbers[] = $assetNum;
                $serialNumbers[] = $serialNum;
            }

            // إدراج العناصر
            for ($i = 1; $i <= $quantity; $i++) {
                $assetNum = $assetNumbers[$i - 1];
                $serialNum = $serialNumbers[$i - 1];

                $itemOrderData = [
                    'order_id' => $orderId,
                    'item_id' => $item->id,
                    'brand' => $this->request->getPost('brand') ?? 'غير محدد',
                    'quantity' => 1,
                    'model_num' => $this->request->getPost("model_num_{$i}") ?? null,
                    'asset_num' => $assetNum,
                    'serial_num' => $serialNum,
                    'room_id' => $this->request->getPost('room'),
                    'assets_type' => 'عهدة عامة',
                    'created_by' => session()->get('employee_id'),
                    'usage_status_id' => 1,
                    'note' => $this->request->getPost('notes') ?? ''
                ];

                $itemOrderId = $this->itemOrderModel->insert($itemOrderData);
                if (!$itemOrderId) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "فشل في إضافة العنصر رقم {$i}"
                    ]);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "تم إنشاء الطلب بنجاح لعدد {$quantity} عنصر",
                'order_id' => $orderId
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in store: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في حفظ الطلب: ' . $e->getMessage()
            ]);
        }
    }

    public function storeMultipleItems()
    {
        try {
            // التحقق من البيانات المطلوبة
            $requiredFields = ['to_employee_id', 'room'];
            foreach ($requiredFields as $field) {
                if (!$this->request->getPost($field)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الحقل {$field} مطلوب"
                    ]);
                }
            }

            $multipleItems = $this->request->getPost('multiple_items');
            if (!$multipleItems) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'يجب إضافة صنف واحد على الأقل'
                ]);
            }

            $itemsData = json_decode($multipleItems, true);
            if (!$itemsData || empty($itemsData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'بيانات الأصناف غير صحيحة'
                ]);
            }

            // إنشاء الطلب الرئيسي
            $orderData = [
                'from_employee_id' => $this->request->getPost('from_employee_id'),
                'to_employee_id' => $this->request->getPost('to_employee_id'),
                'order_status_id' => 1, // جديد
                'note' => $this->request->getPost('notes') ?? ''
            ];

            $orderId = $this->orderModel->insert($orderData);
            if (!$orderId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل في إنشاء الطلب'
                ]);
            }

            // جمع جميع أرقام الأصول والأرقام التسلسلية للتحقق من التكرار
            $allAssetNumbers = [];
            $allSerialNumbers = [];

            foreach ($itemsData as $index => $itemInfo) {
                $assetNum = trim($itemInfo['asset_num'] ?? '');
                $serialNum = trim($itemInfo['serial_num'] ?? '');
                $itemName = trim($itemInfo['item'] ?? '');

                if (!$assetNum || !$serialNum || !$itemName) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "جميع الحقول الأساسية للصنف رقم " . ($index + 1) . " مطلوبة"
                    ]);
                }

                // التحقق من عدم تكرار أرقام الأصول والرقم التسلسلي في نفس الطلب
                if (in_array($assetNum, $allAssetNumbers)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "رقم الأصول {$assetNum} مكرر داخل الطلب"
                    ]);
                }

                if (in_array($serialNum, $allSerialNumbers)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الرقم التسلسلي {$serialNum} مكرر داخل الطلب"
                    ]);
                }

                // التحقق من وجود الأرقام في قاعدة البيانات
                if ($this->itemOrderModel->where('asset_num', $assetNum)->first()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "رقم الأصول {$assetNum} موجود مسبقاً في النظام."
                    ]);
                }

                if ($this->itemOrderModel->where('serial_num', $serialNum)->first()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الرقم التسلسلي {$serialNum} موجود مسبقاً في النظام."
                    ]);
                }

                $allAssetNumbers[] = $assetNum;
                $allSerialNumbers[] = $serialNum;
            }

            // إدراج بيانات الأصناف بعد التحقق
            foreach ($itemsData as $index => $itemInfo) {
                $itemName = trim($itemInfo['item']);

                // البحث عن الصنف أو إنشاؤه
                $item = $this->itemModel->where('name', $itemName)->first();
                if (!$item) {
                    $itemId = $this->itemModel->insert([
                        'name' => $itemName,
                        'minor_category_id' => $itemInfo['minor_category_id'] ?? null
                    ]);
                    if (!$itemId) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => "فشل في إنشاء الصنف: {$itemName}"
                        ]);
                    }
                } else {
                    $itemId = $item->id;
                }

                $itemOrderData = [
                    'order_id' => $orderId,
                    'item_id' => $itemId,
                    'brand' => $itemInfo['brand'] ?? 'غير محدد',
                    'quantity' => 1,
                    'model_num' => $itemInfo['model_num'] ?? null,
                    'asset_num' => trim($itemInfo['asset_num']),
                    'serial_num' => trim($itemInfo['serial_num']),
                    'room_id' => $this->request->getPost('room'),
                    'assets_type' => 'عهدة عامة',
                    'created_by' => session()->get('employee_id'),
                    'usage_status_id' => 1,
                    'note' => $itemInfo['note'] ?? ''
                ];

                $itemOrderId = $this->itemOrderModel->insert($itemOrderData);
                if (!$itemOrderId) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "فشل في إضافة الصنف: {$itemName}"
                    ]);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "تم إنشاء الطلب بنجاح لعدد " . count($itemsData) . " صنف",
                'order_id' => $orderId
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in storeMultipleItems: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في حفظ الطلب: ' . $e->getMessage()
            ]);
        }
    }

    public function editOrder($orderId)
    {
        if (!$orderId) {
            return redirect()->back()->with('error', 'رقم الطلب غير محدد.');
        }

        $order = $this->orderModel->find($orderId);
        if (!$order) {
            return redirect()->back()->with('error', 'الطلب غير موجود.');
        }

        $orderItems = $this->itemOrderModel
            ->select('item_order.*, items.name as item_name')
            ->join('items', 'items.id = item_order.item_id')
            ->where('order_id', $orderId)
            ->findAll();

        // من وجود الموظفين 
        $fromEmployee = $this->employeeModel->find($order->from_employee_id);
        $toEmployee = $this->employeeModel->find($order->to_employee_id);

        // جلب جميع المباني من قاعدة البيانات
        $buildings = $this->buildingModel->findAll();

        // جلب معلومات الموقع
        $locationInfo = null;
        if (!empty($orderItems[0])) {
            $itemOrder = $orderItems[0];
            $room = $this->roomModel->find($itemOrder->room_id);
            if ($room) {
                $section = $this->sectionModel->find($room->section_id);
                $floor = $this->floorModel->find($section->floor_id);
                $building = $this->buildingModel->find($floor->building_id);
                $locationInfo = [
                    'building' => $building,
                    'floor'    => $floor,
                    'section'  => $section,
                    'room'     => $room
                ];
            }
        }

        // جلب جميع التصنيفات الرئيسية
        $majorCategories = $this->majorCategoryModel->findAll();

        return view('warehouse/edit_order_form', [
            'title'           => 'تعديل الطلب',
            'order'           => $order,
            'orderItems'      => $orderItems,
            'fromEmployee'    => $fromEmployee,
            'toEmployee'      => $toEmployee,
            'buildings'       => $buildings,
            'majorCategories' => $majorCategories,
            'locationInfo'    => $locationInfo
        ]);
    }
public function bulkDeleteOrders()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['success' => false, 'message' => 'طلب غير صحيح']);
    }

    if (!session()->get('isLoggedIn')) {
        return $this->response->setJSON(['success' => false, 'message' => 'يجب تسجيل الدخول أولاً']);
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
    public function updateOrder($orderId)
{
    try {
        if (!$orderId) {
            return $this->response->setJSON(['success' => false, 'message' => 'رقم الطلب غير محدد.']);
        }

        $postData = $this->request->getPost();
        
        log_message('info', 'UpdateOrder - Order ID: ' . $orderId);

        // التحقق من الحقول الأساسية
        if (empty($postData['to_employee_id']) || empty($postData['room'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'الحقلين (الرقم الوظيفي للمستلم) و (الغرفة) مطلوبين.'
            ]);
        }

        // التحقق من وجود الطلب
        $existingOrder = $this->orderModel->find($orderId);
        if (!$existingOrder) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'الطلب غير موجود.'
            ]);
        }

        // ✅ الحصول على العناصر الحالية في الطلب من قاعدة البيانات
        $currentOrderItems = $this->itemOrderModel->where('order_id', $orderId)->findAll();
        $currentItemIds = array_column($currentOrderItems, 'item_order_id');
        
        log_message('info', 'Current items in database: ' . implode(', ', $currentItemIds));

        // ✅ الحصول على العناصر المرسلة من الواجهة
        $submittedItemIds = [];
        $existingItemsData = [];
        
        if (!empty($postData['existing_items_data'])) {
            $existingItemsData = json_decode($postData['existing_items_data'], true);
            if (!empty($existingItemsData) && is_array($existingItemsData)) {
                $submittedItemIds = array_map('strval', array_column($existingItemsData, 'item_order_id'));
            }
        }
        
        log_message('info', 'Submitted items from frontend: ' . implode(', ', $submittedItemIds));

        // ✅ حذف العناصر غير المرسلة من الواجهة (المحذوفة)
        $itemsToDelete = array_diff(array_map('strval', $currentItemIds), $submittedItemIds);
        
        if (!empty($itemsToDelete)) {
            log_message('info', 'Deleting items: ' . implode(', ', $itemsToDelete));
            
            foreach ($itemsToDelete as $itemToDelete) {
                $deleteResult = $this->itemOrderModel->delete($itemToDelete);
                if (!$deleteResult) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "فشل في حذف العنصر رقم {$itemToDelete}"
                    ]);
                }
                log_message('info', "Successfully deleted item: {$itemToDelete}");
            }
        }

        // ✅ تحديث بيانات الطلب الرئيسية
        $orderMainData = [
            'to_employee_id' => $postData['to_employee_id'],
            'note' => $postData['notes'] ?? ''
        ];
        
        $orderUpdateResult = $this->orderModel->update($orderId, $orderMainData);
        if (!$orderUpdateResult) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'فشل في تحديث بيانات الطلب الأساسية.'
            ]);
        }

        // ✅ تحديث العناصر المرسلة من الواجهة باستخدام الدالة الجديدة
        if (!empty($existingItemsData) && is_array($existingItemsData)) {
            foreach ($existingItemsData as $index => $itemData) {
                $itemOrderId = $itemData['item_order_id'] ?? null;
                $assetNum = trim($itemData['asset_num'] ?? '');
                $serialNum = trim($itemData['serial_num'] ?? '');
                $brand = trim($itemData['brand'] ?? '');
                $modelNum = trim($itemData['model_num'] ?? '');
                $note = trim($itemData['note'] ?? '');

                if (!$itemOrderId || !$assetNum || !$serialNum) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "جميع الحقول مطلوبة للعنصر رقم " . ($index + 1)
                    ]);
                }

                // التحقق من وجود العنصر
                $existingItem = $this->itemOrderModel->find($itemOrderId);
                if (!$existingItem) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "العنصر رقم {$itemOrderId} غير موجود."
                    ]);
                }

                // ✅ تحديث البيانات باستخدام الدالة الجديدة
                $updateData = [
                    'asset_num' => $assetNum,
                    'serial_num' => $serialNum,
                    'brand' => $brand,
                    'model_num' => $modelNum,
                    'room_id' => $postData['room'],
                    'note' => $note
                ];
                
                log_message('info', "Updating item {$itemOrderId} with data: " . json_encode($updateData));
                
                // ✅ استخدام الدالة الجديدة للتحديث مع فحص التكرار
                $updateResult = $this->itemOrderModel->updateWithUniqueCheck($itemOrderId, $updateData);
                
                if (!$updateResult['success']) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "فشل في تحديث العنصر رقم {$itemOrderId}: " . $updateResult['message']
                    ]);
                }
                
                log_message('info', "Item {$itemOrderId} updated successfully");
            }
        }

        // ✅ إضافة صنف جديد (اختياري) باستخدام الدالة الجديدة
        if (!empty($postData['new_item_data'])) {
            $newItemData = json_decode($postData['new_item_data'], true);
            
            if ($newItemData && is_array($newItemData)) {
                $newItem = trim($newItemData['item'] ?? '');
                $newAsset = trim($newItemData['asset_num'] ?? '');
                $newSerial = trim($newItemData['serial_num'] ?? '');

                if ($newItem && $newAsset && $newSerial) {
                    // البحث عن الصنف أو إنشاؤه
                    $item = $this->itemModel->where('name', $newItem)->first();
                    if (!$item) {
                        $itemId = $this->itemModel->insert(['name' => $newItem]);
                        if (!$itemId) {
                            return $this->response->setJSON([
                                'success' => false,
                                'message' => 'فشل في إنشاء الصنف الجديد.'
                            ]);
                        }
                    } else {
                        $itemId = $item->id;
                    }

                    // ✅ إضافة العنصر الجديد باستخدام الدالة الجديدة
                    $newItemOrderData = [
                        'order_id' => $orderId,
                        'item_id' => $itemId,
                        'asset_num' => $newAsset,
                        'serial_num' => $newSerial,
                        'brand' => trim($newItemData['brand'] ?? 'غير محدد'),
                        'model_num' => trim($newItemData['model_num'] ?? ''),
                        'room_id' => $postData['room'],
                        'assets_type' => 'عهدة عامة',
                        'created_by' => session()->get('employee_id'),
                        'usage_status_id' => 1,
                        'quantity' => 1,
                        'note' => trim($newItemData['note'] ?? '')
                    ];
                    
                    $insertResult = $this->itemOrderModel->insertWithUniqueCheck($newItemOrderData);
                    
                    if (!$insertResult['success']) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'فشل في إضافة الصنف الجديد: ' . $insertResult['message']
                        ]);
                    }
                    
                    log_message('info', "New item added successfully to order {$orderId}");
                }
            }
        }

        log_message('info', "Order {$orderId} updated successfully");

        return $this->response->setJSON([
            'success' => true, 
            'message' => 'تم تحديث الطلب بنجاح', 
            'order_id' => $orderId
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Error in updateOrder: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        return $this->response->setJSON([
            'success' => false, 
            'message' => 'خطأ في تحديث الطلب: ' . $e->getMessage()
        ]);
    }
}

    public function editWarehouseItem($id)
    {
        $data['item'] = $this->itemModel->find($id);
        $data['categories'] = $this->minorCategoryModel->select('minor_category.*, major_category.name as major_category_name')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->findAll();
        return view('editItemView', $data);
    }

    public function updateWarehouseItem($id)
    {
        $postData = $this->request->getPost();
        $entity = new \App\Entities\Items($postData);
        $entity->id = $id;
        $this->itemModel->save($entity);
        return redirect()->to('/warehouse')->with('success', 'تم تحديث العنصر بنجاح');
    }

    private function getWarehouseStats(): array
    {
        $totalQuantityResult = $this->itemOrderModel->selectSum('quantity')->first();
        $totalReceipts = $totalQuantityResult ? (int)$totalQuantityResult->quantity : 0;
        $availableItems = $this->itemOrderModel->countAllResults();
        $totalEntries = $this->itemModel->countAllResults();
        $lowStock = $this->itemOrderModel->where('quantity <', 10)->where('quantity >', 0)->countAllResults();
        $topCategoryResult = $this->itemOrderModel->select('items.minor_category_id, minor_category.name, COUNT(*) as count')
            ->join('items', 'items.id = item_order.item_id')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->groupBy('items.minor_category_id')
            ->orderBy('count', 'ASC')
            ->first();
        $topCategory = $topCategoryResult ? $topCategoryResult->name : 'غير محدد';
        $lastEntry = $this->itemOrderModel->select('item_order.created_at, items.name')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->orderBy('item_order.created_at', 'ASC')
            ->first();
        return [
            'total_receipts' => $totalReceipts,
            'available_items' => $availableItems,
            'total_entries' => $totalEntries,
            'low_stock' => $lowStock,
            'top_category' => $topCategory,
            'last_entry' => $lastEntry ? ['item' => $lastEntry->name ?? 'غير محدد', 'date' => date('Y-m-d H:i', strtotime($lastEntry->created_at))] : null
        ];
    }

    public function searchitems()
    {
        try {
            $term = $this->request->getGet('term');
            if (empty($term) || strlen($term) < 2) {
                return $this->response->setJSON(['success' => false, 'message' => 'يجب إدخال حرفين على الأقل للبحث', 'data' => []]);
            }
            $items = $this->itemModel->like('name', $term)->findAll();
            $itemNames = array_column($items, 'name');
            return $this->response->setJSON(['success' => true, 'data' => $itemNames, 'count' => count($itemNames)]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'خطأ في البحث: ' . $e->getMessage(), 'data' => []]);
        }
    }

    public function searchemployee()
    {
        try {
            $empId = $this->request->getGet('emp_id');
            if (empty($empId)) {
                return $this->response->setJSON(['success' => false, 'message' => 'يجب إدخال رقم الموظف']);
            }
            $employee = $this->employeeModel->where('emp_id', $empId)->first();
            if (!$employee) {
                return $this->response->setJSON(['success' => false, 'message' => 'الموظف غير موجود - الرقم الوظيفي: ' . $empId]);
            }
            return $this->response->setJSON(['success' => true, 'data' => ['name' => $employee->name ?? '', 'email' => $employee->email ?? '', 'transfer_number' => $employee->emp_ext ?? '']]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'خطأ في البحث عن الموظف: ' . $e->getMessage()]);
        }
    }

    public function getformdata()
    {
        try {
            $buildings = $this->buildingModel->findAll();
            $categories = $this->majorCategoryModel->findAll();
            $employees = $this->employeeModel->select('emp_dept')->distinct()->findAll();
            $departments = array_unique(array_column($employees, 'emp_dept'));
            return $this->response->setJSON(['success' => true, 'buildings' => $buildings, 'categories' => $categories, 'departments' => array_values($departments)]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'خطأ في تحميل البيانات: ' . $e->getMessage()]);
        }
    }

    public function getfloorsbybuilding($buildingId = null)
    {
        try {
            if (!$buildingId) {
                return $this->response->setJSON(['success' => false, 'message' => 'رقم المبنى مطلوب', 'data' => []]);
            }
            $floors = $this->floorModel->where('building_id', $buildingId)->findAll();
            return $this->response->setJSON(['success' => true, 'data' => $floors]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'خطأ في تحميل الطوابق: ' . $e->getMessage(), 'data' => []]);
        }
    }

    public function getminorcategoriesbymajor($majorCategoryId = null)
    {
        try {
            if (!$majorCategoryId) {
                return $this->response->setJSON(['success' => false, 'message' => 'رقم التصنيف الرئيسي مطلوب', 'data' => []]);
            }
            $minorCategories = $this->minorCategoryModel->where('major_category_id', $majorCategoryId)->findAll();
            return $this->response->setJSON(['success' => true, 'data' => $minorCategories]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'خطأ في تحميل التصنيفات الفرعية: ' . $e->getMessage(), 'data' => []]);
        }
    }
    
    public function getsectionsbyfloor($floorId = null)
    {
        try {
            if (!$floorId) {
                return $this->response->setJSON(['success' => false, 'message' => 'رقم الطابق مطلوب', 'data' => []]);
            }
            $sections = $this->sectionModel->where('floor_id', $floorId)->findAll();
            return $this->response->setJSON(['success' => true, 'data' => $sections]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'خطأ في تحميل الأقسام: ' . $e->getMessage(), 'data' => []]);
        }
    }

    public function getroomsbysection($sectionId = null)
    {
        try {
            if (!$sectionId) {
                return $this->response->setJSON(['success' => false, 'message' => 'رقم القسم مطلوب', 'data' => []]);
            }
            $rooms = $this->roomModel->where('section_id', $sectionId)->findAll();
            return $this->response->setJSON(['success' => true, 'data' => $rooms]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'خطأ في تحميل الغرف: ' . $e->getMessage(), 'data' => []]);
        }
    }

    private function validateItemUniqueness($assetNum, $serialNum, $ignoreId = null)
    {
        // التحقق من رقم الأصول
        $assetQuery = $this->itemOrderModel->where('asset_num', $assetNum);
        if ($ignoreId) {
            $assetQuery->where('item_order_id !=', $ignoreId);
        }
        $existingAsset = $assetQuery->first();
        if ($existingAsset) {
            throw new \Exception("رقم الأصول {$assetNum} موجود مسبقاً في النظام.");
        }

        // التحقق من الرقم التسلسلي
        $serialQuery = $this->itemOrderModel->where('serial_num', $serialNum);
        if ($ignoreId) {
            $serialQuery->where('item_order_id !=', $ignoreId);
        }
        $existingSerial = $serialQuery->first();
        if ($existingSerial) {
            throw new \Exception("الرقم التسلسلي {$serialNum} موجود مسبقاً في النظام.");
        }
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


    $items = $itemOrderModel->where('order_id', $id)->findAll();

    foreach ($items as $item) {
        $itemData = $itemModel->find($item->item_id);
        $minor    = $itemData ? $minorCatModel->find($itemData->minor_category_id) : null;
        $major    = $minor ? $majorCatModel->find($minor->major_category_id) : null;

        $item->item_name             = $itemData->name ?? 'غير معروف';
        $item->minor_category_name  = $minor->name ?? 'غير معروف';
        $item->major_category_name  = $major->name ?? 'غير معروف';
        $item->room_code            = $roomModel->find($item->room_id)->code ?? 'غير معروف';
        $item->usage_status_name    = $usageStatusModel->find($item->usage_status_id)->usage_status ?? 'غير معروف';
        $item->created_by_name      = $employeeModel->where('emp_id', $item->created_by)->first()->name ?? 'غير معروف';
    }

    
    return view('warehouse/show_order', [
        'order'       => $order,
        'items'       => $items,
        'item_count'  => count($items),
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

    
}}