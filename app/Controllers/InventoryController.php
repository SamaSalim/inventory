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

        return view('warehouseView', [
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
            } // هنا أغلقنا حلقة الـ for

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

        //  من وجود الموظفين 
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

        return view('edit_order_form', [
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


    // public function updateOrder($orderId)
    // {
    //     try {
    //         if (!$orderId) {
    //             return $this->response->setJSON(['success' => false, 'message' => 'رقم الطلب غير محدد.']);
    //         }

    //         $postData = $this->request->getPost();

    //         // تحديث بيانات الطلب الرئيسية
    //         $orderMainData = [
    //             'from_employee_id' => $postData['from_employee_id'],
    //             'to_employee_id'   => $postData['to_employee_id'],
    //             'note'             => $postData['notes'] ?? ''
    //         ];
    //         $this->orderModel->update($orderId, $orderMainData);

    //         // تحديث العناصر الحالية
    //         $existingItemsData = json_decode($this->request->getPost('existing_items_data'), true);
    //         if ($existingItemsData) {
    //             foreach ($existingItemsData as $itemData) {
    //                 $id = $itemData['id'];
    //                 $assetNum = trim($itemData['asset_num'] ?? '');
    //                 $serialNum = trim($itemData['serial_num'] ?? '');
    //                 $brand = trim($itemData['brand'] ?? '');
    //                 $modelNum = trim($itemData['model_num'] ?? '');
    //                 $note = trim($itemData['note'] ?? '');

    //                 if (!$assetNum || !$serialNum) {
    //                     return $this->response->setJSON([
    //                         'success' => false,
    //                         'message' => "جميع الحقول مطلوبة للعنصر رقم {$id}"
    //                     ]);
    //                 }

    //                 $this->validateItemUniqueness($assetNum, $serialNum, $id);

    //                 $this->itemOrderModel->update($id, [
    //                     'asset_num' => $assetNum,
    //                     'serial_num' => $serialNum,
    //                     'brand' => $brand,
    //                     'model_num' => $modelNum,
    //                     'room_id'   => $postData['room'],
    //                     'note'      => $note
    //                 ]);
    //             }
    //         }

    //         // إضافة صنف جديد (اختياري)
    //         if ($this->request->getPost('new_item_data')) {
    //             $newItemData = json_decode($this->request->getPost('new_item_data'), true);

    //             $newItem = trim($newItemData['item'] ?? '');
    //             $newAsset = trim($newItemData['asset_num'] ?? '');
    //             $newSerial = trim($newItemData['serial_num'] ?? '');
    //             $newNote = trim($newItemData['note'] ?? '');
    //             $newBrand = trim($newItemData['brand'] ?? 'غير محدد');
    //             $newModelNum = trim($newItemData['model_num'] ?? '');

    //             if (!$newItem || !$newAsset || !$newSerial) {
    //                 throw new \Exception('جميع الحقول مطلوبة لإضافة صنف جديد.');
    //             }

    //             $this->validateItemUniqueness($newAsset, $newSerial);

    //             $item = $this->itemModel->where('name', $newItem)->first();
    //             if (!$item) {
    //                 $itemId = $this->itemModel->insert(['name' => $newItem]);
    //             } else {
    //                 $itemId = $item->id;
    //             }

    //             $this->itemOrderModel->insert([
    //                 'order_id'       => $orderId,
    //                 'item_id'        => $itemId,
    //                 'asset_num'      => $newAsset,
    //                 'serial_num'     => $newSerial,
    //                 'brand'          => $newBrand,
    //                 'model_num'      => $newModelNum,
    //                 'room_id'        => $postData['room'],
    //                 'assets_type'    => 'عهدة عامة',
    //                 'created_by'     => session()->get('employee_id'),
    //                 'usage_status_id' => 1,
    //                 'quantity'       => 1,
    //                 'note'           => $newNote
    //             ]);
    //         }

    //         return $this->response->setJSON(['success' => true, 'message' => 'تم تحديث الطلب بنجاح', 'order_id' => $orderId]);
    //     } catch (\Exception $e) {
    //         log_message('error', 'Error in updateOrder: ' . $e->getMessage());
    //         return $this->response->setJSON(['success' => false, 'message' => 'خطأ في تحديث الطلب: ' . $e->getMessage()]);
    //     }
    // }
    public function updateOrder($orderId)
    {
        try {
            if (!$orderId) {
                return $this->response->setJSON(['success' => false, 'message' => 'رقم الطلب غير محدد.']);
            }

            $postData = $this->request->getPost();

            // التحقق من الحقول الأساسية
            if (empty($postData['to_employee_id']) || empty($postData['room'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'الحقلين (الرقم الوظيفي للمستلم) و (الغرفة) مطلوبين.'
                ]);
            }

            // تحديث بيانات الطلب الرئيسية
            $orderMainData = [
                'to_employee_id'   => $postData['to_employee_id'],
                'note'             => $postData['notes'] ?? ''
            ];
            $this->orderModel->update($orderId, $orderMainData);

            // تحديث العناصر الحالية
            $existingItemsData = json_decode($postData['existing_items_data'], true);
            if (!empty($existingItemsData)) {
                foreach ($existingItemsData as $itemData) {
                    $id = $itemData['id'] ?? null;
                    $assetNum = trim($itemData['asset_num'] ?? '');
                    $serialNum = trim($itemData['serial_num'] ?? '');
                    $brand = trim($itemData['brand'] ?? '');
                    $modelNum = trim($itemData['model_num'] ?? '');
                    $note = trim($itemData['note'] ?? '');

                    if (!$id || !$assetNum || !$serialNum) {
                        throw new \Exception("جميع الحقول مطلوبة للعناصر الحالية.");
                    }

                    $this->validateItemUniqueness($assetNum, $serialNum, $id);

                    $this->itemOrderModel->update($id, [
                        'asset_num' => $assetNum,
                        'serial_num' => $serialNum,
                        'brand' => $brand,
                        'model_num' => $modelNum,
                        'room_id'   => $postData['room'],
                        'note'      => $note
                    ]);
                }
            }

            // إضافة صنف جديد (اختياري)
            if (isset($postData['new_item_data'])) {
                $newItemData = json_decode($postData['new_item_data'], true);
                $newItem = trim($newItemData['item'] ?? '');
                $newAsset = trim($newItemData['asset_num'] ?? '');
                $newSerial = trim($newItemData['serial_num'] ?? '');
                $newNote = trim($newItemData['note'] ?? '');
                $newBrand = trim($newItemData['brand'] ?? 'غير محدد');
                $newModelNum = trim($newItemData['model_num'] ?? '');

                if (!$newItem || !$newAsset || !$newSerial) {
                    throw new \Exception('جميع الحقول مطلوبة لإضافة صنف جديد.');
                }

                $this->validateItemUniqueness($newAsset, $newSerial);

                $item = $this->itemModel->where('name', $newItem)->first();
                if (!$item) {
                    $itemId = $this->itemModel->insert(['name' => $newItem]);
                } else {
                    $itemId = $item->id;
                }
                if (!$itemId) {
                    throw new \Exception('فشل في إنشاء الصنف الجديد.');
                }

                $this->itemOrderModel->insert([
                    'order_id'       => $orderId,
                    'item_id'        => $itemId,
                    'asset_num'      => $newAsset,
                    'serial_num'     => $newSerial,
                    'brand'          => $newBrand,
                    'model_num'      => $newModelNum,
                    'room_id'        => $postData['room'],
                    'assets_type'    => 'عهدة عامة',
                    'created_by'     => session()->get('employee_id'),
                    'usage_status_id' => 1,
                    'quantity'       => 1,
                    'note'           => $newNote
                ]);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'تم تحديث الطلب بنجاح', 'order_id' => $orderId]);
        } catch (\Exception $e) {
            log_message('error', 'Error in updateOrder: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'خطأ في تحديث الطلب: ' . $e->getMessage()]);
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

    // public function editOrder($orderid){
    //     return view ("edit_order_form");
    // }



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
        $assetQuery = $this->itemOrderModel->where('asset_num', $assetNum);
        if ($ignoreId) {
            $assetQuery->where('item_order_id !=', $ignoreId);
        }
        $existingAsset = $assetQuery->first();
        if ($existingAsset) {
            throw new \Exception("رقم الأصول {$assetNum} موجود مسبقاً في النظام.");
        }
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
    $orderModel = new \App\Models\OrderModel();
    $itemOrderModel = new \App\Models\ItemOrderModel();


    $order = $orderModel
        ->select('
            order.*,
            from_emp.name AS from_name,
            to_emp.name AS to_name,
            order_status.status AS status_name
        ')
        ->join('employee as from_emp', 'from_emp.emp_id = order.from_employee_id', 'left')
        ->join('employee as to_emp', 'to_emp.emp_id = order.to_employee_id', 'left')
        ->join('order_status', 'order_status.id = order.order_status_id', 'left')
        ->where('order.order_id', $id)
        ->first();

    if (!$order) {
        return redirect()->back()->with('error', 'الطلب غير موجود');
    }


    $items = $itemOrderModel
        ->select('
            item_order.*,
            items.name AS item_name,
            minor_category.name AS minor_category_name,
            major_category.name AS major_category_name,
            room.code AS room_code,
            usage_status.usage_status AS usage_status_name,
            creator.name AS created_by_name
        ')
        ->join('items', 'items.id = item_order.item_id', 'left')
        ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
        ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
        ->join('room', 'room.id = item_order.room_id', 'left')
        ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
        ->join('employee as creator', 'creator.emp_id = item_order.created_by', 'left')
        ->where('item_order.order_id', $id)
        ->findAll();

    $itemCount = count($items);

    return view('show_order', [
        'order' => $order,
        'items' => $items,
        'item_count' => $itemCount
    ]);
}
}
