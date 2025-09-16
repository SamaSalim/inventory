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

            // استخدام model transactions (الطريقة الأحدث والأفضل)
            $this->orderModel->transStart();

            // إنشاء الطلب الرئيسي
            $orderData = [
                'from_employee_id' => $this->request->getPost('from_employee_id'),
                'to_employee_id' => $this->request->getPost('to_employee_id'),
                'order_status_id' => 1, // جديد
                'note' => $this->request->getPost('notes') ?? ''
            ];

            $orderId = $this->orderModel->insert($orderData);

            if (!$orderId) {
                $this->orderModel->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل في إنشاء الطلب'
                ]);
            }

            // العثور على الصنف
            $itemName = $this->request->getPost('item');
            $item = $this->itemModel->where('name', $itemName)->first();

            if (!$item) {
                $this->orderModel->transRollback();
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
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "رقم الأصول مطلوب للعنصر رقم {$i}"
                    ]);
                }

                if (empty($serialNum)) {
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الرقم التسلسلي مطلوب للعنصر رقم {$i}"
                    ]);
                }

                // التحقق من عدم تكرار أرقام الأصول
                if (in_array($assetNum, $assetNumbers)) {
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "رقم الأصول {$assetNum} مكرر. يجب أن يكون كل رقم أصول فريد."
                    ]);
                }

                // التحقق من عدم تكرار الأرقام التسلسلية
                if (in_array($serialNum, $serialNumbers)) {
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الرقم التسلسلي {$serialNum} مكرر. يجب أن يكون كل رقم تسلسلي فريد."
                    ]);
                }

                // التحقق من عدم وجود رقم الأصول في قاعدة البيانات
                $existingAsset = $this->itemOrderModel->where('asset_num', $assetNum)->first();
                if ($existingAsset) {
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "رقم الأصول {$assetNum} موجود مسبقاً في النظام."
                    ]);
                }

                // التحقق من عدم وجود الرقم التسلسلي في قاعدة البيانات
                $existingSerial = $this->itemOrderModel->where('serial_num', $serialNum)->first();
                if ($existingSerial) {
                    $this->orderModel->transRollback();
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
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "فشل في إضافة العنصر رقم {$i}"
                    ]);
                }
            }

            $this->orderModel->transComplete();

            if ($this->orderModel->transStatus() === FALSE) {
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

            // استخدام model transactions (الطريقة الأحدث والأفضل)
            $this->orderModel->transStart();

            // إنشاء الطلب الرئيسي
            $orderData = [
                'from_employee_id' => $this->request->getPost('from_employee_id'),
                'to_employee_id' => $this->request->getPost('to_employee_id'),
                'order_status_id' => 1, // جديد
                'note' => $this->request->getPost('notes') ?? ''
            ];

            $orderId = $this->orderModel->insert($orderData);

            if (!$orderId) {
                $this->orderModel->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل في إنشاء الطلب'
                ]);
            }

            // جمع جميع أرقام الأصول والأرقام التسلسلية للتحقق من التكرار
            $allAssetNumbers = [];
            $allSerialNumbers = [];

            foreach ($itemsData as $index => $itemInfo) {
                // التحقق من وجود جميع الحقول المطلوبة
                $requiredItemFields = ['asset_num', 'serial_num', 'item', 'major_category_id', 'minor_category_id'];
                foreach ($requiredItemFields as $field) {
                    if (!isset($itemInfo[$field]) || empty(trim($itemInfo[$field]))) {
                        $this->orderModel->transRollback();
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => "الحقل {$field} مطلوب للصنف رقم " . ($index + 1)
                        ]);
                    }
                }

                $assetNum = trim($itemInfo['asset_num']);
                $serialNum = trim($itemInfo['serial_num']);

                // التحقق من عدم تكرار أرقام الأصول
                if (in_array($assetNum, $allAssetNumbers)) {
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "رقم الأصول {$assetNum} مكرر. يجب أن يكون كل رقم أصول فريد."
                    ]);
                }

                // التحقق من عدم تكرار الأرقام التسلسلية
                if (in_array($serialNum, $allSerialNumbers)) {
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الرقم التسلسلي {$serialNum} مكرر. يجب أن يكون كل رقم تسلسلي فريد."
                    ]);
                }

                // التحقق من عدم وجود رقم الأصول في قاعدة البيانات
                $existingAsset = $this->itemOrderModel->where('asset_num', $assetNum)->first();
                if ($existingAsset) {
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "رقم الأصول {$assetNum} موجود مسبقاً في النظام."
                    ]);
                }

                // التحقق من عدم وجود الرقم التسلسلي في قاعدة البيانات
                $existingSerial = $this->itemOrderModel->where('serial_num', $serialNum)->first();
                if ($existingSerial) {
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الرقم التسلسلي {$serialNum} موجود مسبقاً في النظام."
                    ]);
                }

                $allAssetNumbers[] = $assetNum;
                $allSerialNumbers[] = $serialNum;
            }

            // إدراج البيانات بعد التحقق من صحتها
            foreach ($itemsData as $index => $itemInfo) {
                $itemName = trim($itemInfo['item']);
                
                // البحث عن الصنف أو إنشاؤه إذا لم يكن موجوداً
                $item = $this->itemModel->where('name', $itemName)->first();
                
                if (!$item) {
                    // إنشاء صنف جديد
                    $newItemData = [
                        'name' => $itemName,
                        'minor_category_id' => $itemInfo['minor_category_id']
                    ];
                    
                    $itemId = $this->itemModel->insert($newItemData);
                    if (!$itemId) {
                        $this->orderModel->transRollback();
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
                    'usage_status_id' => 1, // متاح
                    'note' => $itemInfo['note'] ?? ''
                ];

                $itemOrderId = $this->itemOrderModel->insert($itemOrderData);

                if (!$itemOrderId) {
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "فشل في إضافة الصنف: {$itemName}"
                    ]);
                }
            }

            $this->orderModel->transComplete();

            if ($this->orderModel->transStatus() === FALSE) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل في حفظ البيانات'
                ]);
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
   
    public function editOrder($orderid){
        return view ("edit_order_form");
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
            `order`.*,
            from_emp.name AS from_name,
            to_emp.name AS to_name,
            order_status.status AS status_name
        ')
        ->join('employee as from_emp', 'from_emp.emp_id = `order`.from_employee_id', 'left')
        ->join('employee as to_emp', 'to_emp.emp_id = `order`.to_employee_id', 'left')
        ->join('order_status', 'order_status.id = `order`.order_status_id', 'left')
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

?>