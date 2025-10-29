<?php

namespace App\Controllers;

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
    UserModel, // إضافة UserModel
    TransferItemsModel,
    UsageStatusModel
};

use CodeIgniter\HTTP\ResponseInterface;

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
    protected $transferItemsModel;
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
        $this->transferItemsModel = new TransferItemsModel();
        $this->usageStatusModel = new UsageStatusModel(); // تهيئة نموذج حالة الاستخدام
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
            $formattedItems = array_map(function ($item) {
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
     * التحقق من تكرار أرقام الأصول والأرقام التسلسلية
     */
    public function validateAssetSerial()
    {
        try {
            $assetNum = trim($this->request->getPost('asset_num'));
            $serialNum = trim($this->request->getPost('serial_num'));
            $checkType = $this->request->getPost('check_type');
            $excludeItemId = $this->request->getPost('exclude_item_id');

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
                    $assetQuery = $this->itemOrderModel->where('asset_num', $assetNum);

                    // استثناء العنصر الحالي عند التعديل
                    if (!empty($excludeItemId)) {
                        $assetQuery->where('order_id !=', $excludeItemId);
                    }

                    $existingAsset = $assetQuery->first();
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
                    $serialQuery = $this->itemOrderModel->where('serial_num', $serialNum);

                    // استثناء العنصر الحالي عند التعديل
                    if (!empty($excludeItemId)) {
                        $serialQuery->where('order_id !=', $excludeItemId);
                    }

                    $existingSerial = $serialQuery->first();
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
            $toUserId = $this->request->getPost('user_id');
            $roomId = $this->request->getPost('room');
            $notes = $this->request->getPost('notes') ?? '';

            // التحقق من الحقول المطلوبة
            if (empty($toUserId) || empty($roomId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'جميع الحقول الأساسية مطلوبة (المستلم، الغرفة)'
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
                'from_user_id' => $loggedEmployeeId, // من السيشن مباشرة
                'to_user_id' => $toUserId,
                'order_status_id' => 1, // قيد الانتظار
                'note' => $notes
            ];

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

                $itemOrderId = $this->itemOrderModel->insert($itemOrderData);

                if (!$itemOrderId) {
                    $this->orderModel->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'فشل في إضافة أحد العناصر'
                    ]);
                }
            }
            $transferItemData = [
                'item_order_id' => $itemOrderId,
                'from_user_id' => $loggedEmployeeId,
                'to_user_id' => $toUserId,
                'order_status_id' => 1, // قيد الانتظار (في جدول transfer_items)
                'is_opened' => 0
            ];

            if (!$this->transferItemsModel->insert($transferItemData)) {
                $this->orderModel->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل في إنشاء سجل التحويل للعنصر.'
                ]);
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
    /**
     * عرض صفحة إضافة طلب متعدد الأصناف
     */
    public function index()
    {

        $employeeId = session()->get('employee_id');
        $senderData = null;

        if (!empty($employeeId)) {
            // جلب بيانات الموظف 
            $senderData = $this->employeeModel->where('emp_id', $employeeId)->first();
        }

        $data = [
            'sender_data' => $senderData,
        ];

        return view('warehouse/add_multi_item_order', $data);
    }


    /**
     * عرض صفحة تعديل الطلب
     */
    public function editOrder($orderId = null)
    {
        if (!$orderId) {
            return redirect()->to(base_url('inventoryController/index'))->with('error', 'رقم الطلب مطلوب');
        }

        // التحقق من وجود الطلب
        $order = $this->orderModel->find($orderId);
        if (!$order) {
            return redirect()->to(base_url('inventoryController/index'))->with('error', 'الطلب غير موجود');
        }
        $loggedEmployeeId = session()->get('employee_id');

        // جلب بيانات الموظف المسجل دخوله من جدول Employee
        $loggedInUser = null;
        if ($loggedEmployeeId) {
            $loggedInUser = $this->employeeModel->where('emp_id', $loggedEmployeeId)->first();
        }

        return view('warehouse/edit_order', [
            'orderId' => $orderId,
            'loggedInUser' => $loggedInUser, //  تمرير بيانات الموظف المسجل دخوله

        ]);
    }



    /**
     * جلب بيانات الطلب للتعديل
     */
    public function getOrderData($orderId = null)
    {
        try {
            if (!$orderId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'رقم الطلب مطلوب'
                ]);
            }

            // جلب بيانات الطلب كمصفوفة
            $this->orderModel->asArray();
            $order = $this->orderModel->find($orderId);

            if (!$order) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'الطلب غير موجود'
                ]);
            }

            // جلب بيانات المرسل والمستلم (يبقى كما هو)
            $fromUser = null;
            if (isset($order['from_user_id']) && $order['from_user_id']) {
                $this->employeeModel->asArray();
                $fromUser = $this->employeeModel->where('emp_id', $order['from_user_id'])->first();

                if ($fromUser) {
                    $fromUser['user_id'] = $fromUser['emp_id'];
                    $fromUser['user_ext'] = $fromUser['emp_ext'];
                }
            }

            $toUser = null;
            if (isset($order['to_user_id']) && $order['to_user_id']) {
                $this->userModel->asArray();
                $toUser = $this->userModel->where('user_id', $order['to_user_id'])->first();
            }

            // جلب عناصر الطلب - استخدم item_order_id هنا
            $builder = $this->itemOrderModel->builder();
            $orderItems = $builder
                ->select('item_order.*, item_order.item_order_id, items.name as item_name, room.id as room_id, room.code as room_code, 
                     section.id as section_id, floor.id as floor_id, building.id as building_id')
                ->join('items', 'item_order.item_id = items.id', 'left')
                ->join('room', 'item_order.room_id = room.id', 'left')
                ->join('section', 'room.section_id = section.id', 'left')
                ->join('floor', 'section.floor_id = floor.id', 'left')
                ->join('building', 'floor.building_id = building.id', 'left')
                ->where('item_order.order_id', $order['order_id'])
                ->get()
                ->getResultArray();

            // تجميع العناصر حسب الصنف
            $groupedItems = [];
            foreach ($orderItems as $item) {
                $itemKey = $item['item_id'] . '_' . ($item['assets_type'] ?? 'unknown');

                if (!isset($groupedItems[$itemKey])) {
                    $groupedItems[$itemKey] = [
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

                $groupedItems[$itemKey]['quantity']++;
                $groupedItems[$itemKey]['items'][] = [
                    'id' => $item['item_order_id'],
                    'asset_num' => $item['asset_num'],
                    'serial_num' => $item['serial_num'],
                    'model_num' => $item['model_num'],
                    'old_asset_num' => $item['old_asset_num'],
                    'brand' => $item['brand']
                ];
            }

            // إعادة تعيين returnType
            $this->orderModel->asObject();
            $this->userModel->asObject();

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'order' => $order,
                    'from_user' => $fromUser,
                    'to_user' => $toUser,
                    'items' => array_values($groupedItems)
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getOrderData: ' . $e->getMessage() . ' | Line: ' . $e->getLine());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في جلب بيانات الطلب: ' . $e->getMessage()
            ]);
        }
    }
    /**
     * تحديث الطلب متعدد الأصناف
     */
    public function updateMultiItem($orderId = null)
    {
        try {
            // التحقق من تسجيل الدخول
            if (!session()->get('isLoggedIn')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ]);
            }

            if (!$orderId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'رقم الطلب مطلوب'
                ]);
            }

            // التحقق من وجود الطلب
            $existingOrder = $this->orderModel->find($orderId);
            if (!$existingOrder) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'الطلب غير موجود'
                ]);
            }

            // منع التعديل إذا كانت حالة الطلب مقبولة (2) أو قيد الانتظار (1)
            if ($existingOrder->order_status_id == 2) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'لا يمكن تعديل الطلب بعد قبوله من قبل المستلم.'
                ]);
            }

            if ($existingOrder->order_status_id == 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'لا يمكن تعديل الطلب وهو قيد الانتظار. يجب انتظار رد المستلم أولاً.'
                ]);
            }

            // السماح بالتعديل فقط للطلبات المرفوضة (3)

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
            $toUserId = $this->request->getPost('user_id');
            $roomId = $this->request->getPost('room');
            $notes = $this->request->getPost('notes') ?? '';

            // التحقق من الحقول المطلوبة
            if (empty($toUserId) || empty($roomId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'جميع الحقول الأساسية مطلوبة (المستلم، الغرفة)'
                ]);
            }

            // التحقق من تغيير المستلم
            $receiverChanged = ($existingOrder->to_user_id != $toUserId);

            // جمع بيانات الأصناف المُرسلة وتخزينها
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
                    continue;
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

                    // يتم جمع ID العنصر الموجود
                    $existingItemId = $this->request->getPost("existing_item_id_{$itemNum}_{$i}") ?? null;

                    if (empty($assetNum) || empty($serialNum)) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => "رقم الأصول والرقم التسلسلي مطلوبان للعنصر {$i} من الصنف '{$itemName}'"
                        ]);
                    }

                    $items[] = [
                        'existing_item_id' => $existingItemId,
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

            // التحقق من التكرار (داخل الطلب الحالي وفي قاعدة البيانات مع استثناء العناصر القديمة)
            $assetNumbers = [];
            $serialNumbers = [];

            foreach ($items as $index => $item) {
                $assetNum = $item['asset_number'];
                $serialNum = $item['serial_number'];
                $existingItemId = $item['existing_item_id'];

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
                $assetQuery = $this->itemOrderModel->where('asset_num', $assetNum);
                if ($existingItemId) {
                    $assetQuery->where('item_order_id !=', $existingItemId);
                }
                $existingAsset = $assetQuery->first();

                if ($existingAsset) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "رقم الأصول {$assetNum} موجود مسبقاً في النظام"
                    ]);
                }

                $serialQuery = $this->itemOrderModel->where('serial_num', $serialNum);
                if ($existingItemId) {
                    $serialQuery->where('item_order_id !=', $existingItemId);
                }
                $existingSerial = $serialQuery->first();

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

            // تحديث الطلب الرئيسي
            $orderData = [
                'from_user_id' => $loggedEmployeeId,
                'to_user_id' => $toUserId,
                'note' => $notes
            ];

            // إذا تغير المستلم، نعيد الحالة إلى قيد الانتظار
            if ($receiverChanged) {
                $orderData['order_status_id'] = 1; // قيد الانتظار
            }

            unset($orderData['created_at']);
            $this->orderModel->update($orderId, $orderData);

            // جلب العناصر القديمة
            $oldItemOrders = $this->itemOrderModel->where('order_id', $orderId)->findAll();
            $oldItemOrderIds = array_map(function ($item) {
                return (string)$item->item_order_id;
            }, $oldItemOrders);
            $submittedItemOrderIds = [];

            // التحديث والإضافة
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

                $existingItemId = $item['existing_item_id'] ?? null;

                if (!empty($existingItemId) && in_array((string)$existingItemId, $oldItemOrderIds)) {
                    // العنصر موجود: تحديث
                    $updateResult = $this->itemOrderModel->updateWithUniqueCheck($existingItemId, $itemOrderData);

                    if (!$updateResult['success']) {
                        $this->orderModel->transRollback();
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'فشل في تحديث العنصر رقم ' . $existingItemId . ': ' . $updateResult['message']
                        ]);
                    }
                    $submittedItemOrderIds[] = (string)$existingItemId;

                    // تحديث transfer_items للعنصر الموجود
                    if ($receiverChanged) {
                        $this->transferItemsModel->where('item_order_id', $existingItemId)->set([
                            'to_user_id' => $toUserId,
                            'order_status_id' => 1, // قيد الانتظار
                            'is_opened' => 0, // إعادة تعيين حالة الفتح
                            'updated_at' => date('Y-m-d H:i:s')
                        ])->update();
                    }
                } else {
                    // العنصر جديد: إدراج
                    $insertResult = $this->itemOrderModel->insertWithUniqueCheck($itemOrderData);

                    if (!$insertResult['success']) {
                        $this->orderModel->transRollback();
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'فشل في إضافة العنصر الجديد: ' . $insertResult['message']
                        ]);
                    }

                    $newItemOrderId = $insertResult['id'];

                    // إنشاء سجل transfer_items للعنصر الجديد
                    $transferData = [
                        'item_order_id' => $newItemOrderId,
                        'from_user_id' => $loggedEmployeeId,
                        'to_user_id' => $toUserId,
                        'order_status_id' => 1, // قيد الانتظار
                        'is_opened' => 0
                    ];

                    if (!$this->transferItemsModel->insert($transferData)) {
                        $this->orderModel->transRollback();
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'فشل في إنشاء سجل التحويل للعنصر الجديد'
                        ]);
                    }
                }
            }

            // الحذف: حذف العناصر القديمة التي لم تعد موجودة
            $itemsToDelete = array_diff($oldItemOrderIds, $submittedItemOrderIds);

            if (!empty($itemsToDelete)) {
                // حذف سجلات transfer_items المرتبطة أولاً
                foreach ($itemsToDelete as $itemOrderId) {
                    $this->transferItemsModel->where('item_order_id', $itemOrderId)->delete();
                }
                // ثم حذف item_order
                $this->itemOrderModel->delete($itemsToDelete);
            }

            // إنهاء المعاملة
            $this->orderModel->transComplete();

            if ($this->orderModel->transStatus() === FALSE) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل في حفظ التحديثات'
                ]);
            }

            $successMessage = 'تم تحديث الطلب بنجاح';
            if ($receiverChanged) {
                $successMessage .= ' وتم إرساله للمستلم الجديد';
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $successMessage,
                'order_id' => $orderId
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in updateMultiItem: ' . $e->getMessage() . ' | Line: ' . $e->getLine());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في تحديث الطلب: ' . $e->getMessage()
            ]);
        }
    }
}