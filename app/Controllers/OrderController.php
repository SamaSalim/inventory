<?php

namespace App\Controllers;

use App\Models\BuildingModel;
use App\Models\EmployeeModel;
use App\Models\FloorModel;
use App\Models\ItemModel;
use App\Models\ItemOrderModel;
use App\Models\MajorCategoryModel;
use App\Models\OrderModel;
use App\Models\RoomModel;
use App\Models\SectionModel;
use CodeIgniter\HTTP\ResponseInterface;

class OrderController extends BaseController
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
    }

    public function create()
    {
        return view('ordersCreate');
    }

    /**
     * البحث عن الأصناف
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

            $items = $this->itemModel->like('name', $term)->findAll();
            $itemNames = array_column($items, 'name');

            return $this->response->setJSON([
                'success' => true,
                'data' => $itemNames,
                'count' => count($itemNames)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in searchitems: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في البحث: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * البحث عن الموظفين
     */
    public function searchemployee()
{
    try {
        $empId = $this->request->getGet('emp_id');
        
        log_message('info', 'Employee search started for: ' . $empId);
        
        if (empty($empId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'يجب إدخال رقم الموظف'
            ]);
        }

        $employee = $this->employeeModel->where('emp_id', $empId)->first();
        
        log_message('info', 'Employee search result: ' . json_encode($employee));

        if (!$employee) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'الموظف غير موجود - الرقم الوظيفي: ' . $empId
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'name' => $employee->name ?? '',           // استخدم -> بدلاً من []
                'email' => $employee->email ?? '',         // استخدم -> بدلاً من []
                'transfer_number' => $employee->emp_ext ?? '' // استخدم -> بدلاً من []
            ]
        ]);
        
    } catch (\Exception $e) {
        log_message('error', 'Error in searchemployee: ' . $e->getMessage() . ' | Line: ' . $e->getLine() . ' | File: ' . $e->getFile());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'خطأ في البحث عن الموظف: ' . $e->getMessage()
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
            $categories = $this->majorCategoryModel->findAll();
            $employees = $this->employeeModel->select('emp_dept')->distinct()->findAll();
            $departments = array_unique(array_column($employees, 'emp_dept'));

            return $this->response->setJSON([
                'success' => true,
                'buildings' => $buildings,
                'categories' => $categories,
                'departments' => array_values($departments)
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
            // التحقق من البيانات المطلوبة
            $requiredFields = ['employee_id', 'item', 'quantity', 'room'];
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
                'from_employee_id' => $this->request->getPost('employee_id'),
                'to_employee_id' => 'EMP002', // موظف المستودع
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
                    'created_by' => 'EMP002',
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

    
}