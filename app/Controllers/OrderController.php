<?php

namespace App\Controllers;
use CodeIgniter\HTTP\ResponseInterface;

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
    UserModel,
    UsageStatusModel  // إضافة UserModel
};

class OrderController extends BaseController
{
    protected $orderModel;
    protected $itemOrderModel;
    protected $employeeModel;
    protected $userModel; // إضافة UserModel
    protected $itemModel;
    protected $buildingModel;
    protected $floorModel;
    protected $sectionModel;
    protected $roomModel;
    protected $majorCategoryModel;
    protected $usageStatusModel; // إضافة نموذج حالة الاستخدام

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->itemOrderModel = new ItemOrderModel();
        $this->employeeModel = new EmployeeModel();
        $this->userModel = new UserModel(); // تهيئة UserModel
        $this->itemModel = new ItemModel();
        $this->buildingModel = new BuildingModel();
        $this->floorModel = new FloorModel();
        $this->sectionModel = new SectionModel();
        $this->roomModel = new RoomModel();
        $this->majorCategoryModel = new MajorCategoryModel();
        $this->usageStatusModel = new UsageStatusModel(); // تهيئة نموذج حالة الاستخدام
    }

    public function create()
    {
        return view('warehouse\addOrder_1type');
    }

    /**
     * البحث عن الأصناف مع التصنيفات
     */
    public function searchitems()
    {
        try {
            $term = $this->request->getGet('term');
            
            log_message('info', 'Search term: ' . $term);
            
            if (empty($term) || strlen($term) < 2) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'يجب إدخال حرفين على الأقل للبحث',
                    'data' => []
                ]);
            }

            // استخدام query builder للحصول على النتائج مع joins
            $builder = $this->itemModel->builder();
            $items = $builder
                ->select('items.id, items.name, major_category.name as major_category_name, minor_category.name as minor_category_name')
                ->join('minor_category', 'items.minor_category_id = minor_category.id', 'left')
                ->join('major_category', 'minor_category.major_category_id = major_category.id', 'left')
                ->like('items.name', $term)
                ->limit(20)
                ->get()
                ->getResultArray();
            
            log_message('info', 'Items found: ' . count($items));

            if (empty($items)) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => [],
                    'count' => 0,
                    'message' => 'لم يتم العثور على أصناف مطابقة'
                ]);
            }

            // تنسيق البيانات
            $formattedItems = array_map(function($item) {
                return [
                    'name' => $item['name'] ?? 'غير محدد',
                    'major_category' => $item['major_category_name'] ?? 'غير محدد',
                    'minor_category' => $item['minor_category_name'] ?? 'غير محدد'
                ];
            }, $items);

            return $this->response->setJSON([
                'success' => true,
                'data' => $formattedItems,
                'count' => count($formattedItems)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in searchitems: ' . $e->getMessage() . ' | Line: ' . $e->getLine());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في البحث: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * البحث عن المستخدمين (Users) 
     */
    public function searchuser()
    {
        try {
            $userId = $this->request->getGet('user_id');
            
            log_message('info', 'User search started for: ' . $userId);
            
            if (empty($userId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'يجب إدخال رقم المستخدم'
                ]);
            }

            $user = $this->userModel->where('user_id', $userId)->first();
            
            log_message('info', 'User search result: ' . json_encode($user));

            if (!$user) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'المستخدم غير موجود - الرقم: ' . $userId
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'name' => $user->name ?? '',
                    'email' => $user->email ?? '',
                    'transfer_number' => $user->user_ext ?? ''
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in searchuser: ' . $e->getMessage() . ' | Line: ' . $e->getLine() . ' | File: ' . $e->getFile());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في البحث عن المستخدم: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * الحصول على بيانات النموذج الأولية
     */
    public function getformdata()
    {
        try {
            $buildings = $this->buildingModel->findAll();
          
            // جلب قيم enum من المودل مباشرة
            $custodyTypes = $this->itemOrderModel->getAssetsTypeEnum();

            return $this->response->setJSON([
                'success' => true,
                'buildings' => $buildings,
                'custody_types' => $custodyTypes
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getformdata: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في تحميل البيانات: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * الحصول على الطوابق حسب المبنى
     */
    public function getfloorsbybuilding($buildingId = null)
    {
        try {
            if (!$buildingId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'رقم المبنى مطلوب',
                    'data' => []
                ]);
            }

            $floors = $this->floorModel->where('building_id', $buildingId)->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $floors
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getfloorsbybuilding: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في تحميل الطوابق: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * الحصول على الأقسام حسب الطابق
     */
    public function getsectionsbyfloor($floorId = null)
    {
        try {
            if (!$floorId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'رقم الطابق مطلوب',
                    'data' => []
                ]);
            }

            $sections = $this->sectionModel->where('floor_id', $floorId)->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $sections
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getsectionsbyfloor: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في تحميل الأقسام: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * الحصول على الغرف حسب القسم
     */
    public function getroomsbysection($sectionId = null)
    {
        try {
            if (!$sectionId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'رقم القسم مطلوب',
                    'data' => []
                ]);
            }

            $rooms = $this->roomModel->where('section_id', $sectionId)->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $rooms
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getroomsbysection: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في تحميل الغرف: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * حفظ طلب جديد
     */
    public function store()
    {
        try {
            // التحقق من تسجيل الدخول
            if (! session()->get('isLoggedIn')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ]);
            }
            
            // الحصول على معرف الموظف من الجلسة
            $loggedEmployeeId = session()->get('employee_id');
            
            // الحصول على حالة الاستخدام الافتراضية (أول سجل)
            $defaultUsageStatus = $this->usageStatusModel->first();
            
            if (!$defaultUsageStatus) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'لا توجد حالات استخدام في النظام'
                ]);
            }

            // التحقق من البيانات المطلوبة
            $requiredFields = ['from_user_id', 'user_id', 'item', 'quantity', 'room'];
            foreach ($requiredFields as $field) {
                if (!$this->request->getPost($field)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الحقل {$field} مطلوب"
                    ]);
                }
            }

            // بدء المعاملة
            $this->orderModel->transStart();
            
            // إنشاء الطلب الرئيسي
            $orderData = [
                'from_user_id' => $this->request->getPost('from_user_id'),
                'to_user_id' => $this->request->getPost('user_id'), // to_user_id هو المستلم
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
            $custodyType = $this->request->getPost('custody_type');
            $assetNumbers = [];
            $serialNumbers = [];

            // جمع جميع أرقام الأصول والأرقام التسلسلية للتحقق
            for ($i = 1; $i <= $quantity; $i++) {
                $assetNum = trim($this->request->getPost("asset_num_{$i}"));
                $serialNum = trim($this->request->getPost("serial_num_{$i}"));
                
                // التحقق من رقم الأصول
                if (empty($assetNum)) {
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "رقم الأصول مطلوب للعنصر رقم {$i}"
                    ]);
                }
                
                // التحقق من صحة تنسيق رقم الأصول
                $assetValidation = $this->validateAssetNumber($assetNum);
                if (!$assetValidation['valid']) {
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "العنصر رقم {$i}: " . $assetValidation['message']
                    ]);
                }
                
                // التحقق من الرقم التسلسلي
                if (empty($serialNum)) {
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الرقم التسلسلي مطلوب للعنصر رقم {$i}"
                    ]);
                }
                
                // التحقق من صحة تنسيق الرقم التسلسلي
                $serialValidation = $this->validateSerialNumber($serialNum);
                if (!$serialValidation['valid']) {
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "العنصر رقم {$i}: " . $serialValidation['message']
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
                $modelNum = trim($this->request->getPost("model_num_{$i}")) ?? '';
                $oldAssetNum = trim($this->request->getPost("old_asset_num_{$i}")) ?? '';
                $brand = trim($this->request->getPost("brand_{$i}")) ?? 'غير محدد';

                $itemOrderData = [
                    'order_id' => $orderId,
                    'item_id' => $item->id,
                    'brand' => $brand,
                    'quantity' => 1,
                    'model_num' => $modelNum,
                    'asset_num' => $assetNum,
                    'old_asset_num' => $oldAssetNum,
                    'serial_num' => $serialNum,
                    'room_id' => $this->request->getPost('room'),
                    'assets_type' => $custodyType,
                    'created_by' => $loggedEmployeeId,
                    'usage_status_id' => $defaultUsageStatus->id,
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

            // إنهاء المعاملة
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

    /**
     * التحقق من تكرار أرقام الأصول والأرقام التسلسلية
     */
    public function validateAssetSerial()
    {
        try {
            $assetNum = trim($this->request->getPost('asset_num'));
            $serialNum = trim($this->request->getPost('serial_num'));
            $checkType = $this->request->getPost('check_type');
            
            if (empty($assetNum) && empty($serialNum)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'يجب إدخال رقم الأصول أو الرقم التسلسلي'
                ]);
            }

            $errors = [];

            // التحقق من رقم الأصول
            if (!empty($assetNum) && ($checkType === 'asset' || $checkType === 'both')) {
                // التحقق من صحة تنسيق رقم الأصول أولاً
                $assetValidation = $this->validateAssetNumber($assetNum);
                
                if (!$assetValidation['valid']) {
                    $errors[] = [
                        'type' => 'asset',
                        'message' => $assetValidation['message']
                    ];
                } else {
                    // التحقق من التكرار في قاعدة البيانات
                    $existingAsset = $this->itemOrderModel->where('asset_num', $assetNum)->first();
                    if ($existingAsset) {
                        $errors[] = [
                            'type' => 'asset',
                            'message' => "رقم الأصول {$assetNum} موجود مسبقاً في النظام"
                        ];
                    }
                }
            }

            // التحقق من الرقم التسلسلي
            if (!empty($serialNum) && ($checkType === 'serial' || $checkType === 'both')) {
                // التحقق من صحة تنسيق الرقم التسلسلي أولاً
                $serialValidation = $this->validateSerialNumber($serialNum);
                
                if (!$serialValidation['valid']) {
                    $errors[] = [
                        'type' => 'serial',
                        'message' => $serialValidation['message']
                    ];
                } else {
                    // التحقق من التكرار في قاعدة البيانات
                    $existingSerial = $this->itemOrderModel->where('serial_num', $serialNum)->first();
                    if ($existingSerial) {
                        $errors[] = [
                            'type' => 'serial', 
                            'message' => "الرقم التسلسلي {$serialNum} موجود مسبقاً في النظام"
                        ];
                    }
                }
            }

            if (!empty($errors)) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $errors
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'الأرقام متاحة للاستخدام'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in validateAssetSerial: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في التحقق من البيانات: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * التحقق من رقم الأصول - يجب أن يكون 12 رقم فقط
     */
    private function validateAssetNumber($assetNum)
    {
        // التحقق من أن الرقم يحتوي على أرقام فقط
        if (!preg_match('/^\d+$/', $assetNum)) {
            return [
                'valid' => false,
                'message' => 'رقم الأصول يجب أن يحتوي على أرقام فقط'
            ];
        }
        
        // التحقق من أن الرقم 12 خانة بالضبط
        if (strlen($assetNum) !== 12) {
            return [
                'valid' => false,
                'message' => 'رقم الأصول يجب أن يكون 12 رقم بالضبط (العدد الحالي: ' . strlen($assetNum) . ')'
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'رقم الأصول صحيح'
        ];
    }

    /**
     * التحقق من الرقم التسلسلي - يسمح بالأحرف والأرقام
     */
    private function validateSerialNumber($serialNum)
    {
        // نمط للرموز الغريبة - يسمح بالأحرف والأرقام والمسافات والشرطات
        if (preg_match('/[!@#$%^&*()+=\[\]{}|\\:";\'<>?,.\\/~`]/', $serialNum)) {
            return [
                'valid' => false,
                'message' => 'الرقم التسلسلي يحتوي على رموز غير مسموحة'
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'الرقم التسلسلي صحيح'
        ];
    }

/**
     * حفظ الطلب متعدد الأصناف
     */
    public function storeMultiItem()
    {
        try {
            // التحقق من تسجيل الدخول
            if (!session()->get('isLoggedIn')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ]);
            }

            $loggedEmployeeId = session()->get('employee_id');
            
            // الحصول على حالة الاستخدام الافتراضية
            $defaultUsageStatus = $this->usageStatusModel->first();
            if (!$defaultUsageStatus) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'لا توجد حالات استخدام في النظام'
                ]);
            }

            // الحصول على البيانات الأساسية
            $fromUserId = $this->request->getPost('from_user_id');
            $toUserId = $this->request->getPost('user_id');
            $roomId = $this->request->getPost('room');
            $notes = $this->request->getPost('notes') ?? '';

            // التحقق من الحقول المطلوبة
            if (empty($fromUserId) || empty($toUserId) || empty($roomId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'جميع الحقول الأساسية مطلوبة (المرسل، المستلم، الغرفة)'
                ]);
            }

            // جمع بيانات الأصناف من النموذج
            $items = [];
            $allPostData = $this->request->getPost();
            
            // البحث عن جميع الأصناف في البيانات المرسلة
            $itemNumbers = [];
            foreach ($allPostData as $key => $value) {
                if (preg_match('/^item_(\d+)$/', $key, $matches)) {
                    $itemNumbers[] = $matches[1];
                }
            }

            // معالجة كل صنف
            foreach ($itemNumbers as $itemNum) {
                $itemName = $this->request->getPost("item_{$itemNum}");
                $quantity = (int)$this->request->getPost("quantity_{$itemNum}");
                $custodyType = $this->request->getPost("custody_type_{$itemNum}");

                if (empty($itemName) || $quantity <= 0 || empty($custodyType)) {
                    continue; // تجاهل الأصناف غير المكتملة
                }

                // العثور على الصنف في قاعدة البيانات
                $item = $this->itemModel->where('name', $itemName)->first();
                if (!$item) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الصنف '{$itemName}' غير موجود في قاعدة البيانات"
                    ]);
                }

                // جمع بيانات العناصر لهذا الصنف
                for ($i = 1; $i <= $quantity; $i++) {
                    $assetNum = trim($this->request->getPost("asset_num_{$itemNum}_{$i}"));
                    $serialNum = trim($this->request->getPost("serial_num_{$itemNum}_{$i}"));
                    $modelNum = trim($this->request->getPost("model_num_{$itemNum}_{$i}") ?? '');
                    $oldAssetNum = trim($this->request->getPost("old_asset_num_{$itemNum}_{$i}") ?? '');
                    $brand = trim($this->request->getPost("brand_{$itemNum}_{$i}") ?? 'غير محدد');

                    if (empty($assetNum) || empty($serialNum)) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => "رقم الأصول والرقم التسلسلي مطلوبان للعنصر {$i} من الصنف '{$itemName}'"
                        ]);
                    }
                    // إضافة العنصر إلى القائمة  - itemOrder  ليتم معالجته لاحقاً
                    $items[] = [
                        'item_id' => $item->id,
                        'item_name' => $itemName,
                        'asset_number' => $assetNum,
                        'serial_number' => $serialNum,
                        'model_number' => $modelNum,
                        'old_asset_number' => $oldAssetNum,
                        'brand' => $brand,
                        'custody_type' => $custodyType,
                        'quantity' => 1
                    ];
                }
            }

            if (empty($items)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'لم يتم العثور على أي أصناف صالحة لإضافتها'
                ]);
            }

            // التحقق من عدم تكرار الأرقام
            $assetNumbers = [];
            $serialNumbers = [];

            foreach ($items as $index => $item) {
                $assetNum = $item['asset_number'];
                $serialNum = $item['serial_number'];

                // التحقق من صحة تنسيق الأرقام
                $assetValidation = $this->validateAssetNumber($assetNum);
                if (!$assetValidation['valid']) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "العنصر " . ($index + 1) . ": " . $assetValidation['message']
                    ]);
                }

                $serialValidation = $this->validateSerialNumber($serialNum);
                if (!$serialValidation['valid']) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "العنصر " . ($index + 1) . ": " . $serialValidation['message']
                    ]);
                }

                // التحقق من التكرار في الطلب الحالي
                if (in_array($assetNum, $assetNumbers)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "رقم الأصول {$assetNum} مكرر في الطلب"
                    ]);
                }

                if (in_array($serialNum, $serialNumbers)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الرقم التسلسلي {$serialNum} مكرر في الطلب"
                    ]);
                }

                // التحقق من التكرار في قاعدة البيانات
                $existingAsset = $this->itemOrderModel->where('asset_num', $assetNum)->first();
                if ($existingAsset) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "رقم الأصول {$assetNum} موجود مسبقاً في النظام"
                    ]);
                }

                $existingSerial = $this->itemOrderModel->where('serial_num', $serialNum)->first();
                if ($existingSerial) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "الرقم التسلسلي {$serialNum} موجود مسبقاً في النظام"
                    ]);
                }

                $assetNumbers[] = $assetNum;
                $serialNumbers[] = $serialNum;
            }

            // بدء المعاملة
            $this->orderModel->transStart();

            // إنشاء الطلب الرئيسي
            $orderData = [
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
                'order_status_id' => 1,
                'note' => $notes
            ];

            // إدخال الطلب الرئيسي
            $orderId = $this->orderModel->insert($orderData);

            if (!$orderId) {
                $this->orderModel->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل في إنشاء الطلب الرئيسي'
                ]);
            }

            // إضافة كل عنصر
            foreach ($items as $item) {
                $itemOrderData = [
                    'order_id' => $orderId,
                    'item_id' => $item['item_id'],
                    'brand' => $item['brand'],
                    'quantity' => $item['quantity'],
                    'model_num' => $item['model_number'],
                    'asset_num' => $item['asset_number'],
                    'old_asset_num' => $item['old_asset_number'],
                    'serial_num' => $item['serial_number'],
                    'room_id' => $roomId,
                    'assets_type' => $item['custody_type'],
                    'created_by' => $loggedEmployeeId,
                    'usage_status_id' => $defaultUsageStatus->id,
                    'note' => $notes
                ];
                // 
                $itemOrderId = $this->itemOrderModel->insert($itemOrderData);

                if (!$itemOrderId) {
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'فشل في إضافة أحد العناصر'
                    ]);
                }
            }

            // إنهاء المعاملة
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
            log_message('error', 'Error in storeMultiItem: ' . $e->getMessage() . ' | Line: ' . $e->getLine());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في حفظ الطلب: ' . $e->getMessage()
            ]);
        }
    }

    public function index()
    {
        return view('warehouse/addMultiItemOrder');
    }

}