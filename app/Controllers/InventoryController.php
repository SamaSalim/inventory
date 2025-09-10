<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BuildingModel;
use App\Models\EmployeeModel;
use App\Models\FloorModel;
use App\Models\ItemModel;
use App\Models\ItemOrderModel;
use App\Models\MajorCategoryModel;
use App\Models\OrderModel;
use App\Models\RoomModel;
use App\Models\SectionModel;
use App\Models\OrderStatusModel;
use App\Models\UsageStatusModel;
use CodeIgniter\HTTP\ResponseInterface;

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

    public function store()
    {
        try {
            // التحقق من البيانات المطلوبة
            $requiredFields = ['to_employee_id', 'item', 'quantity', 'room'];
            foreach ($requiredFields as $field) {
                if (!$this->request->getPost($field)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الحقل {$field} مطلوب"
                    ]);
                }
            }

            $db = \Config\Database::connect();
            $db->transStart();

            // إنشاء الطلب الرئيسي
            $orderData = [
                'from_employee_id' => $this->request->getPost('from_employee_id'),
                'to_employee_id' => $this->request->getPost('to_employee_id'),
                'order_status_id' => 1, // جديد
                'note' => $this->request->getPost('notes') ?? ''
            ];

            $orderId = $this->orderModel->insert($orderData);

            if (!$orderId) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل في إنشاء الطلب'
                ]);
            }

            // العثور على الصنف
            $itemName = $this->request->getPost('item');
            $item = $this->itemModel->where('name', $itemName)->first();

            if (!$item) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'الصنف المحدد غير موجود'
                ]);
            }

            // إضافة العناصر حسب الكمية
            $quantity = (int)$this->request->getPost('quantity');
            $assetNumbers = [];
            $serialNumbers = [];

            // جمع جميع أرقام الأصول والأرقام التسلسلية للتحقق من التكرار
            for ($i = 1; $i <= $quantity; $i++) {
                $assetNum = trim($this->request->getPost("asset_num_{$i}"));
                $serialNum = trim($this->request->getPost("serial_num_{$i}"));

                // التحقق من إدخال رقم الأصول والرقم التسلسلي (إجباري)
                if (empty($assetNum)) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "رقم الأصول مطلوب للعنصر رقم {$i}"
                    ]);
                }

                if (empty($serialNum)) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الرقم التسلسلي مطلوب للعنصر رقم {$i}"
                    ]);
                }

                // التحقق من عدم تكرار أرقام الأصول
                if (in_array($assetNum, $assetNumbers)) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "رقم الأصول {$assetNum} مكرر. يجب أن يكون كل رقم أصول فريد."
                    ]);
                }

                // التحقق من عدم تكرار الأرقام التسلسلية
                if (in_array($serialNum, $serialNumbers)) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الرقم التسلسلي {$serialNum} مكرر. يجب أن يكون كل رقم تسلسلي فريد."
                    ]);
                }

                // التحقق من عدم وجود رقم الأصول في قاعدة البيانات
                $existingAsset = $this->itemOrderModel->where('asset_num', $assetNum)->first();
                if ($existingAsset) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "رقم الأصول {$assetNum} موجود مسبقاً في النظام."
                    ]);
                }

                // التحقق من عدم وجود الرقم التسلسلي في قاعدة البيانات
                $existingSerial = $this->itemOrderModel->where('serial_num', $serialNum)->first();
                if ($existingSerial) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الرقم التسلسلي {$serialNum} موجود مسبقاً في النظام."
                    ]);
                }

                $assetNumbers[] = $assetNum;
                $serialNumbers[] = $serialNum;
            }

            // إدراج البيانات بعد التحقق من صحتها
            for ($i = 1; $i <= $quantity; $i++) {
                $assetNum = trim($this->request->getPost("asset_num_{$i}"));
                $serialNum = trim($this->request->getPost("serial_num_{$i}"));

                $itemOrderData = [
                    'order_id' => $orderId,
                    'item_id' => $item->id,
                    'brand' => $this->request->getPost('brand') ?? 'غير محدد',
                    'quantity' => 1,
                    'model_num' => $this->request->getPost("model_num_{$i}"),
                    'asset_num' => $assetNum,
                    'serial_num' => $serialNum,
                    'room_id' => $this->request->getPost('room'),
                    'assets_type' => 'عهدة عامة',
                    'created_by' => session()->get('employee_id'),
                    'usage_status_id' => 1, // متاح
                    'note' => $this->request->getPost('notes') ?? ''
                ];

                $itemOrderId = $this->itemOrderModel->insert($itemOrderData);

                if (!$itemOrderId) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "فشل في إضافة العنصر رقم {$i}"
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل في حفظ البيانات'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'تم إنشاء الطلب بنجاح',
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

    public function updateOrder($orderId)
    {
        if (!$orderId) {
            return $this->response->setJSON(['success' => false, 'message' => 'رقم الطلب غير محدد.']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $postData = $this->request->getPost();

            // التحقق من الحقول المطلوبة الأساسية فقط (بدون item و quantity)
            $requiredFields = ['to_employee_id', 'room'];
            foreach ($requiredFields as $field) {
                if (empty($postData[$field])) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الحقل {$field} مطلوب"
                    ]);
                }
            }

            // تحديث بيانات الطلب الأساسية
            $orderMainData = [
                'from_employee_id' => $postData['from_employee_id'],
                'to_employee_id' => $postData['to_employee_id'],
                'note' => $postData['notes'] ?? '',
            ];

            $this->orderModel->update($orderId, $orderMainData);

            // تحديث العناصر الموجودة - التعامل مع البيانات المرسلة مباشرة من النموذج
            $existingItems = $this->itemOrderModel->where('order_id', $orderId)->findAll();

            foreach ($existingItems as $item) {
                $itemId = $item->item_order_id;

                // البحث عن البيانات الجديدة للعنصر الحالي بالطريقة الصحيحة من النموذج
                $assetNumField = "existing_asset_num[{$itemId}]";
                $serialNumField = "existing_serial_num[{$itemId}]";
                $notesField = "existing_notes[{$itemId}]";

                if (isset($postData[$assetNumField]) && isset($postData[$serialNumField])) {
                    $newAssetNum = trim($postData[$assetNumField]);
                    $newSerialNum = trim($postData[$serialNumField]);

                    if (empty($newAssetNum) || empty($newSerialNum)) {
                        $db->transRollback();
                        return $this->response->setJSON(['success' => false, 'message' => 'يجب ملء جميع حقول العناصر الموجودة.']);
                    }

                    // التحقق من عدم التكرار (باستثناء العنصر الحالي)
                    $this->validateItemUniqueness($newAssetNum, $newSerialNum, $itemId);

                    // تحديث بيانات العنصر
                    $this->itemOrderModel->update($itemId, [
                        'asset_num' => $newAssetNum,
                        'serial_num' => $newSerialNum,
                        'room_id' => $postData['room'],
                        'note' => $postData[$notesField] ?? ''
                    ]);
                }
            }

            // إضافة العناصر الجديدة (اختياري تماماً) - تحقق من النموذج مباشرة
            $newItemName = trim($postData['new_item'] ?? '');
            $newAssetNum = trim($postData['new_asset_num'] ?? '');
            $newSerialNum = trim($postData['new_serial_num'] ?? '');
            $newItemNotes = trim($postData['new_item_notes'] ?? '');

            // فقط إذا تم إدخال بيانات العنصر الجديد
            if ($newItemName || $newAssetNum || $newSerialNum) {
                // إذا تم إدخال أي بياناتـ يجب أن تكون كاملة
                if (empty($newItemName) || empty($newAssetNum) || empty($newSerialNum)) {
                    $db->transRollback();
                    return $this->response->setJSON(['success' => false, 'message' => 'يجب ملء جميع حقول العناصر الجديدة (الصنف، رقم الأصول، الرقم التسلسلي) أو تركها فارغة تماماً.']);
                }

                $itemInfo = $this->itemModel->where('name', $newItemName)->first();
                if (!$itemInfo) {
                    $db->transRollback();
                    return $this->response->setJSON(['success' => false, 'message' => 'الصنف الجديد غير موجود.']);
                }

                $this->validateItemUniqueness($newAssetNum, $newSerialNum);
                $this->itemOrderModel->insert([
                    'order_id' => $orderId,
                    'item_id' => $itemInfo->id,
                    'asset_num' => $newAssetNum,
                    'serial_num' => $newSerialNum,
                    'room_id' => $postData['room'],
                    'assets_type' => 'عهدة عامة',
                    'created_by' => session()->get('employee_id'),
                    'usage_status_id' => 1,
                    'quantity' => 1,
                    'note' => $newItemNotes
                ]);
            }

            $db->transComplete();
            if ($db->transStatus() === FALSE) {
                return $this->response->setJSON(['success' => false, 'message' => 'فشل في حفظ البيانات.']);
            }
            return $this->response->setJSON(['success' => true, 'message' => 'تم تحديث الطلب بنجاح!', 'order_id' => $orderId]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'خطأ في تحديث الطلب: ' . $e->getMessage()]);
        }
    }
    // باقي الدوال تبقى كما هي...
    public function warehouseDashboard()
    {
        // تم تحديث ترتيب البيانات للحصول على آخر الطلبات أولاً
        $itemOrders = $this->itemOrderModel->select('
                item_order.*, 
                item_order.order_id,
                items.name as item_name,
                minor_category.name as category_name,
                major_category.name as major_category_name,
                usage_status.usage_status,
                room.code as room_code,
                employee.name as created_by_name
            ')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left')
            ->join('room', 'room.id = item_order.room_id', 'left')
            ->join('employee', 'employee.emp_id = item_order.created_by', 'left')
            ->orderBy('item_order.item_order_id', 'DESC')
            ->findAll();

        $categories = $this->minorCategoryModel->select('minor_category.*, major_category.name as major_category_name')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->findAll();

        $stats = $this->getWarehouseStats();

        $statuses = $this->orderStatusModel->findAll();
        $usageStatuses = $this->usageStatusModel->findAll();

        return view('warehouseView', [
            'categories' => $categories,
            'items' => $itemOrders,
            'stats' => $stats,
            'statuses' => $statuses,
            'usage_statuses' => $usageStatuses,
        ]);
    }

    public function editOrder($orderId)
    {
        if (!$orderId) {
            return redirect()->back()->with('error', 'رقم الطلب غير محدد.');
        }

        $orderData = $this->orderModel->where('order_id', $orderId)->first();
        if (!$orderData) {
            return redirect()->back()->with('error', 'الطلب غير موجود.');
        }

        $orderItems = $this->itemOrderModel
            ->select('item_order.*, items.name as item_name')
            ->join('items', 'items.id = item_order.item_id')
            ->where('order_id', $orderId)
            ->findAll();

        $employee = $this->employeeModel->where('emp_id', $orderData->from_employee_id)->first();

        $viewData = [
            'mode' => 'edit',
            'order' => $orderData,
            'orderItems' => $orderItems,
            'employee' => $employee,
        ];

        return view('order_form', $viewData);
    }

    public function addItemToWarehouse()
    {
        $postData = $this->request->getPost();
        $postData['created_by'] = session()->get('employee_id');
        $itemType = $this->request->getPost('item_type');

        try {
            if ($itemType === 'new_item') {
                $itemEntity = new \App\Entities\Items([
                    'name' => $postData['name'],
                    'minor_category_id' => $postData['minor_category_id']
                ]);
                $this->itemModel->insert($itemEntity);
                return redirect()->to('/warehouse')->with('success', 'تمت إضافة الصنف الجديد بنجاح');
            } else {
                $itemOrderEntity = new \App\Entities\ItemOrder([
                    'order_id' => $postData['order_id'] ?? null,
                    'item_id' => $postData['item_id'],
                    'brand' => $postData['brand'] ?? null,
                    'quantity' => $postData['quantity'] ?? 1,
                    'model_num' => $postData['model_num'] ?? null,
                    'asset_num' => $postData['asset_num'],
                    'serial_num' => $postData['serial_num'],
                    'old_asset_num' => $postData['old_asset_num'] ?? null,
                    'room_id' => $postData['room_id'],
                    'assets_type' => $postData['assets_type'] ?? 'غير محدد',
                    'created_by' => $postData['created_by'],
                    'usage_status_id' => $postData['usage_status_id'] ?? 1,
                    'note' => $postData['note'] ?? null
                ]);
                $this->itemOrderModel->insert($itemOrderEntity);
                return redirect()->to('/warehouse')->with('success', 'تمت إضافة طلب الصنف بنجاح');
            }
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->back()->with('error', 'حدث خطأ في قاعدة البيانات: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ غير متوقع');
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
            ->orderBy('count', 'DESC')
            ->first();
        $topCategory = $topCategoryResult ? $topCategoryResult->name : 'غير محدد';
        $lastEntry = $this->itemOrderModel->select('item_order.created_at, items.name')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->orderBy('item_order.created_at', 'DESC')
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
}
