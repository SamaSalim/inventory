<?php

namespace App\Services;

use App\Models\{
    BuildingModel,
    EmployeeModel,
    FloorModel,
    ItemModel,
    ItemOrderModel,
    OrderModel,
    RoomModel,
    SectionModel,
    UserModel,
    TransferItemsModel,
    UsageStatusModel
};
use App\Exceptions\AuthenticationException;

class OrderService
{
    protected $orderModel;
    protected $itemOrderModel;
    protected $employeeModel;
    protected $userModel;
    protected $itemModel;
    protected $buildingModel;
    protected $floorModel;
    protected $sectionModel;
    protected $roomModel;
    protected $transferItemsModel;
    protected $usageStatusModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->itemOrderModel = new ItemOrderModel();
        $this->employeeModel = new EmployeeModel();
        $this->userModel = new UserModel();
        $this->itemModel = new ItemModel();
        $this->buildingModel = new BuildingModel();
        $this->floorModel = new FloorModel();
        $this->sectionModel = new SectionModel();
        $this->roomModel = new RoomModel();
        $this->transferItemsModel = new TransferItemsModel();
        $this->usageStatusModel = new UsageStatusModel();
    }

    /* ============================================================
     * PAGES
     * ============================================================ */
    public function showOrderPage()
    {
        $employeeId = session()->get('employee_id');
        $senderData = $employeeId ? $this->employeeModel->where('emp_id', $employeeId)->first() : null;

        return view('warehouse/add_multi_item_order', ['sender_data' => $senderData]);
    }

    public function showEditOrderPage($orderId)
    {
        if (!session()->get('isLoggedIn')) throw new AuthenticationException();
        if (!$orderId) return redirect()->to(base_url('inventoryController/index'))->with('error', 'رقم الطلب مطلوب');

        $order = $this->orderModel->find($orderId);
        if (!$order) return redirect()->to(base_url('inventoryController/index'))->with('error', 'الطلب غير موجود');

        $loggedEmployeeId = session()->get('employee_id');
        $loggedInUser = $loggedEmployeeId ? $this->employeeModel->where('emp_id', $loggedEmployeeId)->first() : null;

        return view('warehouse/edit_order', [
            'to_user_id' => $order->to_user_id,
            'orderId' => $orderId,
            'loggedInUser' => $loggedInUser,
            'orderStatusId' => $order->order_status_id,
        ]);
    }

    /* ============================================================
     * SEARCH
     * ============================================================ */
    public function searchItems($request)
    {
        try {
            $term = $request->getGet('term');
            if (empty($term) || strlen($term) < 2) {
                return response()->setJSON(['success' => false, 'message' => 'يجب إدخال حرفين على الأقل', 'data' => []]);
            }

            $items = $this->itemModel->builder()
                ->select('items.id, items.name, major_category.name as major_category_name, minor_category.name as minor_category_name')
                ->join('minor_category', 'items.minor_category_id = minor_category.id', 'left')
                ->join('major_category', 'minor_category.major_category_id = major_category.id', 'left')
                ->like('items.name', $term)
                ->limit(20)
                ->get()
                ->getResultArray();

            $formattedItems = array_map(fn($item) => [
                'name' => $item['name'] ?? 'غير محدد',
                'major_category' => $item['major_category_name'] ?? 'غير محدد',
                'minor_category' => $item['minor_category_name'] ?? 'غير محدد'
            ], $items);

            return response()->setJSON(['success' => true, 'data' => $formattedItems, 'count' => count($formattedItems)]);
        } catch (\Exception $e) {
            return response()->setJSON(['success' => false, 'message' => 'خطأ في البحث: ' . $e->getMessage()]);
        }
    }

    public function searchUser($request)
    {
        try {
            $userId = $request->getGet('user_id');
            if (empty($userId)) return response()->setJSON(['success' => false, 'message' => 'يجب إدخال رقم المستخدم']);

            $user = $this->userModel->where('user_id', $userId)->first();
            if (!$user) return response()->setJSON(['success' => false, 'message' => 'المستخدم غير موجود']);

            return response()->setJSON(['success' => true, 'data' => [
                'name' => $user->name ?? '',
                'email' => $user->email ?? '',
                'transfer_number' => $user->user_ext ?? ''
            ]]);
        } catch (\Exception $e) {
            return response()->setJSON(['success' => false, 'message' => 'خطأ في البحث: ' . $e->getMessage()]);
        }
    }

    /* ============================================================
     * FORM DATA
     * ============================================================ */
    public function getFormData()
    {
        try {
            return response()->setJSON([
                'success' => true,
                'buildings' => $this->buildingModel->findAll(),
                'custody_types' => $this->itemOrderModel->getAssetsTypeEnum()
            ]);
        } catch (\Exception $e) {
            return response()->setJSON(['success' => false, 'message' => 'خطأ في تحميل البيانات']);
        }
    }

    public function getFloorsByBuilding($buildingId)
    {
        if (!$buildingId) return response()->setJSON(['success' => false, 'message' => 'رقم المبنى مطلوب']);
        return response()->setJSON(['success' => true, 'data' => $this->floorModel->where('building_id', $buildingId)->findAll()]);
    }

    public function getSectionsByFloor($floorId)
    {
        if (!$floorId) return response()->setJSON(['success' => false, 'message' => 'رقم الطابق مطلوب']);
        return response()->setJSON(['success' => true, 'data' => $this->sectionModel->where('floor_id', $floorId)->findAll()]);
    }

    public function getRoomsBySection($sectionId)
    {
        if (!$sectionId) return response()->setJSON(['success' => false, 'message' => 'رقم القسم مطلوب']);
        return response()->setJSON(['success' => true, 'data' => $this->roomModel->where('section_id', $sectionId)->findAll()]);
    }

    /* ============================================================
     * VALIDATION
     * ============================================================ */

    /**
     * ✅ التحقق من رقم الأصل/الرقم التسلسلي (مع إعادة تفعيل التحقق من رقم الأصل فقط)
     */
    public function validateAssetSerial($request)
    {
        try {
            $assetNum = trim($request->getPost('asset_num'));
            $serialNum = trim($request->getPost('serial_num'));
            $checkType = $request->getPost('check_type');
            $excludeItemId = $request->getPost('exclude_item_id'); // يستخدم للتحقق من التكرار

            if (empty($assetNum) && empty($serialNum)) {
                return response()->setJSON(['success' => false, 'message' => 'يجب إدخال رقم الأصول أو الرقم التسلسلي']);
            }

            $errors = [];

            if (!empty($assetNum) && ($checkType === 'asset' || $checkType === 'both')) {
                // 1. التحقق من التنسيق (12 رقم فقط)
                $validation = $this->validateAssetNumber($assetNum);
                if (!$validation['valid']) {
                    $errors[] = ['type' => 'asset', 'message' => $validation['message']];
                } else {
                    // 2. التحقق من التكرار في قاعدة البيانات
                    $query = $this->itemOrderModel->where('asset_num', $assetNum);
                    if ($excludeItemId) $query->where('item_order_id !=', $excludeItemId);

                    $existingItem = $query->first();

                    if ($existingItem) $errors[] = ['type' => 'asset', 'message' => "رقم الأصول موجود مسبقاً"];
                }
            }

            // التحقق من تنسيق الرقم التسلسلي
            if (!empty($serialNum) && ($checkType === 'serial' || $checkType === 'both')) {
                $validation = $this->validateSerialNumber($serialNum);
                if (!$validation['valid']) $errors[] = ['type' => 'serial', 'message' => $validation['message']];
            }

            return response()->setJSON(
                empty($errors) ? ['success' => true, 'message' => 'الأرقام متاحة'] : ['success' => false, 'errors' => $errors]
            );
        } catch (\Exception $e) {
            return response()->setJSON(['success' => false, 'message' => 'خطأ في التحقق']);
        }
    }

    /**
     * ✅ التحقق من رقم الأصول: 12 رقمًا ولا يحتوي على كاركتر
     */
    private function validateAssetNumber($assetNum)
    {
        if (!preg_match('/^\d{12}$/', $assetNum)) {
            $length = strlen($assetNum);
            if (!preg_match('/^\d+$/', $assetNum)) {
                return ['valid' => false, 'message' => 'رقم الأصول يجب أن يحتوي على أرقام فقط'];
            }
            if ($length !== 12) {
                return ['valid' => false, 'message' => 'رقم الأصول يجب أن يكون 12 رقم (الحالي: ' . $length . ')'];
            }
            return ['valid' => false, 'message' => 'رقم الأصول غير صالح (يجب أن يكون 12 رقمًا بدون رموز)'];
        }
        return ['valid' => true];
    }

    /**
     *  التحقق من الرقم التسلسلي (تم إزالة التحقق من تكراره)
     */
    private function validateSerialNumber($serialNum)
    {
        if (preg_match('/[!@#$%^&*()+=\[\]{}|\\:";\'<>?,.\\/~`]/', $serialNum)) {
            return ['valid' => false, 'message' => 'الرقم التسلسلي يحتوي على رموز غير مسموحة'];
        }
        return ['valid' => true];
    }

    /* ============================================================
     * GET ORDER DATA
     * ============================================================ */
    public function getOrderData($orderId)
    {
        try {
            if (!$orderId) return response()->setJSON(['success' => false, 'message' => 'رقم الطلب مطلوب']);

            $order = $this->orderModel->asArray()->find($orderId);
            if (!$order) return response()->setJSON(['success' => false, 'message' => 'الطلب غير موجود']);

            $fromUser = $order['from_user_id'] ? $this->employeeModel->asArray()->where('emp_id', $order['from_user_id'])->first() : null;
            if ($fromUser) {
                $fromUser['user_id'] = $fromUser['emp_id'];
                $fromUser['user_ext'] = $fromUser['emp_ext'];
            }

            $toUser = $order['to_user_id'] ? $this->userModel->asArray()->where('user_id', $order['to_user_id'])->first() : null;

            $orderItems = $this->itemOrderModel->builder()
                ->select('item_order.*, items.name as item_name, room.id as room_id, room.code as room_code, 
                     section.id as section_id, floor.id as floor_id, building.id as building_id')
                ->join('items', 'item_order.item_id = items.id', 'left')
                ->join('room', 'item_order.room_id = room.id', 'left')
                ->join('section', 'room.section_id = section.id', 'left')
                ->join('floor', 'section.floor_id = floor.id', 'left')
                ->join('building', 'floor.building_id = building.id', 'left')
                ->where('item_order.order_id', $order['order_id'])
                ->get()
                ->getResultArray();

            $groupedItems = $this->groupOrderItems($orderItems);

            $this->orderModel->asObject();
            $this->userModel->asObject();

            return response()->setJSON(['success' => true, 'data' => compact('order', 'fromUser', 'toUser') + ['items' => $groupedItems]]);
        } catch (\Exception $e) {
            return response()->setJSON(['success' => false, 'message' => 'خطأ في جلب البيانات']);
        }
    }

    private function groupOrderItems($orderItems)
    {
        $grouped = [];
        foreach ($orderItems as $item) {
            $key = $item['item_id'] . '_' . ($item['assets_type'] ?? 'unknown');
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'item_id' => $item['item_id'],
                    'item_name' => $item['item_name'],
                    'assets_type' => $item['assets_type'],
                    'building_id' => $item['building_id'],
                    'floor_id' => $item['floor_id'],
                    'section_id' => $item['section_id'],
                    'room_id' => $item['room_id'],
                    'room_code' => $item['room_code'],
                    'quantity' => 0,
                    'items' => []
                ];
            }
            $grouped[$key]['quantity']++;
            $grouped[$key]['items'][] = [
                'id' => $item['item_order_id'],
                'asset_num' => $item['asset_num'],
                'serial_num' => $item['serial_num'],
                'model_num' => $item['model_num'],
                'old_asset_num' => $item['old_asset_num'],
                'brand' => $item['brand'],
                'price' => $item['price']
            ];
        }
        return array_values($grouped);
    }

    /* ============================================================
     * STORE & UPDATE (مختصرة)
     * ============================================================ */
    public function storeMultiItem($request)
    {
        try {
            if (!session()->get('isLoggedIn')) throw new AuthenticationException();

            $data = $this->prepareOrderData($request);
            $items = $this->collectItems($request);
            $this->validateItems($items);

            return $this->saveOrder($data, $items);
        } catch (\Exception $e) {
            log_message('error', 'storeMultiItem: ' . $e->getMessage());
            return response()->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateMultiItem($orderId, $request)
    {
        try {
            if (!session()->get('isLoggedIn')) throw new AuthenticationException();
            if (!$orderId) return response()->setJSON(['success' => false, 'message' => 'رقم الطلب مطلوب']);

            $order = $this->orderModel->find($orderId);
            if (!$order) return response()->setJSON(['success' => false, 'message' => 'الطلب غير موجود']);
            if ($order->order_status_id == 2) return response()->setJSON(['success' => false, 'message' => 'لا يمكن تعديل طلب مقبول']);
            if (!in_array($order->order_status_id, [1, 3])) return response()->setJSON(['success' => false, 'message' => 'حالة غير صالحة للتعديل']);

            $data = $this->prepareOrderData($request);
            $items = $this->collectItems($request, true);
            $this->validateItems($items, $orderId);

            return $this->updateOrder($orderId, $order, $data, $items);
        } catch (\Exception $e) {
            log_message('error', 'updateMultiItem: ' . $e->getMessage());
            return response()->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /* ============================================================
     * HELPERS
     * ============================================================ */
    private function prepareOrderData($request)
    {
        $toUserId = $request->getPost('user_id');
        $roomId = $request->getPost('room');

        if (empty($toUserId) || empty($roomId)) throw new \Exception('المستلم والغرفة مطلوبان');

        return [
            'to_user_id' => $toUserId,
            'room_id' => $roomId,
            'note' => $request->getPost('notes') ?? '',
            'from_user_id' => session()->get('employee_id')
        ];
    }

    private function collectItems($request, $withIds = false)
    {
        $items = [];
        foreach ($request->getPost() as $key => $value) {
            if (preg_match('/^item_(\d+)$/', $key, $m)) {
                $num = $m[1];
                $qty = (int)$request->getPost("quantity_{$num}");
                $itemName = $request->getPost("item_{$num}");
                $custodyType = $request->getPost("custody_type_{$num}");

                if (empty($itemName) || $qty <= 0) continue;

                $item = $this->itemModel->where('name', $itemName)->first();
                if (!$item) throw new \Exception("الصنف '{$itemName}' غير موجود");

                for ($i = 1; $i <= $qty; $i++) {
                    $price = trim($request->getPost("price_{$num}_{$i}"));

                    //  التحقق من السعر: يجب أن يكون رقمياً موجباً إذا لم يكن فارغاً
                    if (!empty($price) && (!is_numeric($price) || $price < 0)) {
                        throw new \Exception("سعر العنصر رقم " . ($i) . " غير صالح أو يحتوي على رموز غير مسموحة.");
                    }

                    // تحويل القيمة الفارغة إلى null لحفظها في قاعدة البيانات
                    $finalPrice = !empty($price) ? $price : null;

                    $items[] = [
                        'item_id' => $item->id,
                        'asset_num' => trim($request->getPost("asset_num_{$num}_{$i}")),
                        'serial_num' => trim($request->getPost("serial_num_{$num}_{$i}")),
                        'model_num' => trim($request->getPost("model_num_{$num}_{$i}") ?? ''),
                        'old_asset_num' => trim($request->getPost("old_asset_num_{$num}_{$i}") ?? ''),
                        'brand' => trim($request->getPost("brand_{$num}_{$i}") ?? 'غير محدد'),
                        'price' => $finalPrice,
                        'custody_type' => $custodyType,
                        'existing_id' => $withIds ? $request->getPost("existing_item_id_{$num}_{$i}") : null
                    ];
                }
            }
        }
        return $items;
    }

    /**
     *  إعادة التحقق من رقم الأصل (Asset Number)
     */
    private function validateItems($items, $excludeOrderId = null)
    {
        $assetNums = [];
        foreach ($items as $i => $item) {
            if (empty($item['asset_num']) || empty($item['serial_num'])) throw new \Exception("بيانات العنصر " . ($i + 1) . " غير كاملة");

            // 1. التحقق من تنسيق رقم الأصل (12 رقم فقط)
            $assetCheck = $this->validateAssetNumber($item['asset_num']);
            if (!$assetCheck['valid']) throw new \Exception("العنصر " . ($i + 1) . ": " . $assetCheck['message']);

            // 2. التحقق من تنسيق الرقم التسلسلي
            $serialCheck = $this->validateSerialNumber($item['serial_num']);
            if (!$serialCheck['valid']) throw new \Exception("العنصر " . ($i + 1) . ": " . $serialCheck['message']);

            // 3. التحقق من عدم تكرار رقم الأصل داخل الطلب نفسه
            if (in_array($item['asset_num'], $assetNums)) throw new \Exception("رقم الأصول {$item['asset_num']} مكرر داخل هذا الطلب");

            // 4. التحقق من عدم تكرار رقم الأصل في قاعدة البيانات
            $query = $this->itemOrderModel->where('asset_num', $item['asset_num']);
            if ($item['existing_id'] ?? null) $query->where('item_order_id !=', $item['existing_id']);

            if ($excludeOrderId) {
                if ($item['existing_id'] ?? null) {
                    $query->where('item_order_id !=', $item['existing_id']);
                } else {
                    $query->where('order_id !=', $excludeOrderId);
                }
            }

            if ($query->first()) throw new \Exception("رقم الأصول {$item['asset_num']} موجود مسبقاً في قاعدة البيانات");

            $assetNums[] = $item['asset_num'];
        }
    }

    private function saveOrder($data, $items)
    {
        $this->orderModel->transStart();

        $orderId = $this->orderModel->insert($data + ['order_status_id' => 1]);
        if (!$orderId) throw new \Exception('فشل إنشاء الطلب');

        $usageStatus = $this->usageStatusModel->first();

        foreach ($items as $item) {
            $itemOrderId = $this->itemOrderModel->insert([
                'order_id' => $orderId,
                'item_id' => $item['item_id'],
                'asset_num' => $item['asset_num'],
                'serial_num' => $item['serial_num'],
                'model_num' => $item['model_num'],
                'old_asset_num' => $item['old_asset_num'],
                'brand' => $item['brand'],
                'price' => $item['price'],
                'room_id' => $data['room_id'],
                'assets_type' => $item['custody_type'],
                'usage_status_id' => $usageStatus->id,
                'quantity' => 1,
                'created_by' => $data['from_user_id'],
                'note' => $data['note']
            ]);

            $this->transferItemsModel->insert([
                'item_order_id' => $itemOrderId,
                'from_user_id' => $data['from_user_id'],
                'to_user_id' => $data['to_user_id'],
                'order_status_id' => 1,
                'is_opened' => 0
            ]);
        }

        $this->orderModel->transComplete();
        return response()->setJSON(['success' => true, 'message' => 'تم إنشاء الطلب بنجاح', 'order_id' => $orderId]);
    }

    private function updateOrder($orderId, $order, $data, $items)
    {
        $receiverChanged = ($order->to_user_id != $data['to_user_id']);
        if ($order->order_status_id == 1 && $receiverChanged) {
            return response()->setJSON(['success' => false, 'message' => 'لا يمكن تغيير المستلم في حالة الانتظار']);
        }

        $this->orderModel->transStart();

        //  تحديث بيانات الطلب مع الاحتفاظ بالبيانات الأساسية
        $updateData = [
            'to_user_id' => $data['to_user_id'],
            'note' => $data['note']
        ];

        // إعادة تعيين الحالة فقط إذا تغير المستلم وكانت الحالة مرفوضة
        if ($receiverChanged && $order->order_status_id == 3) {
            $updateData['order_status_id'] = 1;
        }

        $this->orderModel->update($orderId, $updateData);

        $oldIds = array_column($this->itemOrderModel->where('order_id', $orderId)->findAll(), 'item_order_id');
        $newIds = [];

        $usageStatus = $this->usageStatusModel->first();

        foreach ($items as $item) {
            $itemData = [
                'order_id' => $orderId,
                'item_id' => $item['item_id'],
                'asset_num' => $item['asset_num'],
                'serial_num' => $item['serial_num'],
                'model_num' => $item['model_num'],
                'old_asset_num' => $item['old_asset_num'],
                'brand' => $item['brand'],
                'price' => $item['price'], // هنا يمكن أن تكون القيمة null
                'room_id' => $data['room_id'],
                'assets_type' => $item['custody_type'],
                'usage_status_id' => $usageStatus->id,
                'quantity' => 1,
                'created_by' => $data['from_user_id'],
                'note' => $data['note']
            ];

            if ($item['existing_id'] && in_array($item['existing_id'], $oldIds)) {
                $this->itemOrderModel->update($item['existing_id'], $itemData);
                $newIds[] = $item['existing_id'];
                if ($receiverChanged && $order->order_status_id == 3) {
                    $this->transferItemsModel->where('item_order_id', $item['existing_id'])
                        ->set(['to_user_id' => $data['to_user_id'], 'order_status_id' => 1, 'is_opened' => 0])->update();
                }
            } else {
                $newItemId = $this->itemOrderModel->insert($itemData);
                $newIds[] = $newItemId;
                $this->transferItemsModel->insert([
                    'item_order_id' => $newItemId,
                    'from_user_id' => $data['from_user_id'],
                    'to_user_id' => $data['to_user_id'],
                    'order_status_id' => 1,
                    'is_opened' => 0
                ]);
            }
        }

        $toDelete = array_diff($oldIds, $newIds);
        if ($toDelete) {
            foreach ($toDelete as $id) $this->transferItemsModel->where('item_order_id', $id)->delete();
            $this->itemOrderModel->delete($toDelete);
        }

        $this->orderModel->transComplete();
        return response()->setJSON(['success' => true, 'message' => 'تم التحديث بنجاح', 'order_id' => $orderId]);
    }
}
