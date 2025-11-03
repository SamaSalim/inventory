<?php

namespace App\Controllers\Return\SuperWarehouse;

use App\Controllers\BaseController;
use App\Models\ItemOrderModel;
use App\Models\UsageStatusModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;
use App\Models\OrderModel;
use App\Models\TransferItemsModel;
use App\Models\HistoryModel;

class ReissueItems extends BaseController
{
    protected $itemOrderModel;
    protected $usageStatusModel;
    protected $employeeModel;
    protected $userModel;
    protected $orderModel;
    protected $transferItemsModel;
    protected $historyModel; 

    public function __construct()
    {
        $this->itemOrderModel = new ItemOrderModel();
        $this->usageStatusModel = new UsageStatusModel();
        $this->employeeModel = new EmployeeModel();
        $this->userModel = new UserModel();
        $this->orderModel = new OrderModel();
        $this->transferItemsModel = new TransferItemsModel();
        $this->historyModel = new HistoryModel(); 
    }


    public function index(): string
    {
        // جلب بيانات المرسل من الجلسة
        $senderEmployeeId = session()->get('employee_id');
        $senderData = null;
        
        if ($senderEmployeeId) {
            $senderData = $this->employeeModel
                ->select('employee.*, employee.email')
                ->where('employee.emp_id', $senderEmployeeId)
                ->first();
        }
        
        // جلب الفلاتر من الطلب
        $filters = [
            'general_search' => $this->request->getGet('general_search'),
            'order_id'       => $this->request->getGet('order_id'),
            'asset_num'      => $this->request->getGet('asset_num'),
            'serial_num'     => $this->request->getGet('serial_num'),
            'item_name'      => $this->request->getGet('item_name'),
            'brand'          => $this->request->getGet('brand'),
            'model'          => $this->request->getGet('model'),
            'date_from'      => $this->request->getGet('date_from'),
            'date_to'        => $this->request->getGet('date_to')
        ];

        // بناء الاستعلام - جلب العناصر الفردية في حالة "معاد صرفه" (4)
        $builder = $this->itemOrderModel
            ->select('
                item_order.item_order_id,
                item_order.order_id,
                item_order.created_at,
                item_order.asset_num,
                item_order.serial_num,
                item_order.brand,
                item_order.model_num as model,
                items.name AS item_name
            ')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->where('item_order.usage_status_id', 4); // فقط "معاد صرفه"

        // فلتر البحث العام
        if (!empty($filters['general_search'])) {
            $searchTerm = $filters['general_search'];
            $builder->groupStart()
                ->like('item_order.item_order_id', $searchTerm)
                ->orLike('item_order.asset_num', $searchTerm)
                ->orLike('item_order.serial_num', $searchTerm)
                ->orLike('items.name', $searchTerm)
                ->orLike('item_order.brand', $searchTerm)
                ->orLike('item_order.model_num', $searchTerm)
                ->groupEnd();
        }

        // فلتر حسب رقم الطلب
        if (!empty($filters['order_id'])) {
            $builder->like('item_order.item_order_id', $filters['order_id']);
        }

        // فلتر حسب Asset Number
        if (!empty($filters['asset_num'])) {
            $builder->like('item_order.asset_num', $filters['asset_num']);
        }

        // فلتر حسب Serial Number
        if (!empty($filters['serial_num'])) {
            $builder->like('item_order.serial_num', $filters['serial_num']);
        }

        // فلتر حسب اسم العنصر
        if (!empty($filters['item_name'])) {
            $builder->like('items.name', $filters['item_name']);
        }

        // فلتر حسب Brand
        if (!empty($filters['brand'])) {
            $builder->like('item_order.brand', $filters['brand']);
        }

        // فلتر حسب Model
        if (!empty($filters['model'])) {
            $builder->like('item_order.model_num', $filters['model']);
        }

        // فلتر حسب تاريخ البداية
        if (!empty($filters['date_from'])) {
            $builder->where('DATE(item_order.created_at) >=', $filters['date_from']);
        }

        // فلتر حسب تاريخ النهاية
        if (!empty($filters['date_to'])) {
            $builder->where('DATE(item_order.created_at) <=', $filters['date_to']);
        }

        // جلب النتائج - عناصر فردية
        $reissuedItems = $builder
            ->orderBy('item_order.created_at', 'DESC')
            ->asArray()
            ->findAll();

        // جلب الحالات المرجعية
        $usageStatuses = $this->usageStatusModel->asArray()->findAll();

        // عرض الصفحة مع البيانات
        return view('warehouse/super_warehouse/reissue_items', [
            'returnOrders'  => $reissuedItems,
            'usageStatuses' => $usageStatuses,
            'filters'       => $filters,
            'sender_data'   => $senderData
        ]);
    }
    
    /**
     * البحث عن المستخدم أو الموظف
     */
    public function searchUser()
    {
        $userId = $this->request->getGet('user_id');
        
        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'رقم المستخدم مطلوب'
            ]);
        }

        // البحث في جدول الموظفين أولاً
        $employeeData = $this->employeeModel
            ->select('employee.emp_id as id, employee.name, employee.emp_ext, employee.email')
            ->where('employee.emp_id', $userId)
            ->first();

        if ($employeeData) {
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'id' => $employeeData->id ?? '',
                    'name' => $employeeData->name ?? '',
                    'email' => $employeeData->email ?? '',
                    'transfer_number' => $employeeData->emp_ext ?? ''
                ],
                'type' => 'employee'
            ]);
        }

        // إذا لم يتم العثور عليه في الموظفين، ابحث في جدول المستخدمين
        $userData = $this->userModel
            ->select('users.user_id as id, users.name, users.user_ext, users.email')
            ->where('users.user_id', $userId)
            ->first();

        if ($userData) {
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'id' => $userData->id ?? '',
                    'name' => $userData->name ?? '',
                    'email' => $userData->email ?? '',
                    'transfer_number' => $userData->user_ext ?? ''
                ],
                'type' => 'user'
            ]);
        }

        // لم يتم العثور على المستخدم في كلا الجدولين
        return $this->response->setJSON([
            'success' => false,
            'message' => 'المستخدم غير موجود'
        ]);
    }

    /**
     * إرسال طلب إعادة صرف واحد لجميع العناصر المحددة
     */
    public function sendReissueOrder()
    {
        try {
            log_message('info', 'Reissue order request received from IP: ' . $this->request->getIPAddress());

            if (!session()->get('isLoggedIn')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ]);
            }

            $fromUserId = session()->get('employee_id');
            
            if (!$fromUserId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'خطأ في الحصول على بيانات المرسل'
                ]);
            }

            $receiverUserId = $this->request->getPost('receiver_user_id');
            $itemOrderIdsJson = $this->request->getPost('item_order_ids');
            
            if (!$receiverUserId || !$itemOrderIdsJson) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'بيانات غير مكتملة'
                ]);
            }

            $itemOrderIds = json_decode($itemOrderIdsJson, true);
            
            if (empty($itemOrderIds) || !is_array($itemOrderIds)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'لم يتم تحديد أي عناصر'
                ]);
            }

            log_message('info', 'Attempting to reissue items: ' . implode(',', $itemOrderIds) . ' | From: ' . $fromUserId . ' | To: ' . $receiverUserId);

            $receiver = $this->userModel->where('user_id', $receiverUserId)->first();
            if (!$receiver) {
                $receiver = $this->employeeModel->where('emp_id', $receiverUserId)->first();
                if (!$receiver) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'المستلم غير موجود في النظام'
                    ]);
                }
            }

            $items = $this->itemOrderModel
                ->whereIn('item_order_id', $itemOrderIds)
                ->where('usage_status_id', 4)
                ->findAll();

            if (count($items) !== count($itemOrderIds)) {
                log_message('warning', 'Some items are not eligible for reissue. Expected: ' . count($itemOrderIds) . ', Found: ' . count($items));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'بعض العناصر المحددة غير صالحة لإعادة الصرف'
                ]);
            }

            // ✅ استخدام db من itemOrderModel بدلاً من property منفصل
            $this->itemOrderModel->db->transStart();

            // حذف السجلات القديمة في transfer_items
            foreach ($itemOrderIds as $itemOrderId) {
                $this->itemOrderModel->db->table('transfer_items')
                    ->where('item_order_id', $itemOrderId)
                    ->delete();
                
                log_message('info', "Deleted old transfer_items records for item_order_id: {$itemOrderId}");
            }

            // إنشاء طلب جديد
            $orderData = [
                'from_user_id' => $fromUserId,
                'to_user_id' => $receiverUserId,
                'order_status_id' => 1,
                'note' => 'إعادة صرف - تم الإنشاء من قبل المخزون الرئيسي'
            ];

            $newOrderId = $this->orderModel->insert($orderData);

            if (!$newOrderId) {
                $this->itemOrderModel->db->transRollback();
                log_message('error', 'Failed to create order in database');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل في إنشاء الطلب'
                ]);
            }

            log_message('info', 'Order created successfully with ID: ' . $newOrderId);

            // تحديث العناصر
            $successCount = 0;
            
            foreach ($items as $item) {
                $updateData = [
                    'order_id' => $newOrderId,
                    'usage_status_id' => 1,
                    'created_by' => $fromUserId,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $updated = $this->itemOrderModel->update($item->item_order_id, $updateData);

                if (!$updated) {
                    $this->itemOrderModel->db->transRollback();
                    log_message('error', 'Failed to update item_order_id: ' . $item->item_order_id);
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'فشل في تحديث العنصر رقم ' . $item->item_order_id
                    ]);
                }

                // ✅ إضافة سجل في جدول history لكل عنصر
                $historyData = [
                    'item_order_id' => $item->item_order_id,
                    'usage_status_id' => 1, // الحالة الجديدة
                    'handled_by' => $fromUserId
                ];

                $historyInserted = $this->historyModel->insert($historyData);

                if (!$historyInserted) {
                    log_message('warning', 'Failed to insert history for item_order_id: ' . $item->item_order_id);
                    // ⚠️ لا نفشل العملية بأكملها إذا فشل تسجيل التاريخ
                }

                $successCount++;
            }

            // إنشاء سجل transfer_items
            $firstItem = $items[0];
            
            $transferData = [
                'item_order_id' => $firstItem->item_order_id,
                'from_user_id' => $fromUserId,
                'to_user_id' => $receiverUserId,
                'order_status_id' => 1,
                'is_opened' => 0
            ];

            $transferId = $this->transferItemsModel->insert($transferData);

            if (!$transferId) {
                $this->itemOrderModel->db->transRollback();
                log_message('error', 'Failed to create transfer_items for order: ' . $newOrderId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل في إنشاء سجل التحويل للطلب'
                ]);
            }

            $this->itemOrderModel->db->transComplete();

            if ($this->itemOrderModel->db->transStatus() === false) {
                log_message('error', 'Transaction failed for order_id: ' . $newOrderId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'فشل في حفظ البيانات'
                ]);
            }

            log_message('info', "✅ Reissue order completed successfully. Order ID: {$newOrderId}, Items count: {$successCount}, From: {$fromUserId}, To: {$receiverUserId}");

            return $this->response->setJSON([
                'success' => true,
                'message' => "تم إرسال الطلب بنجاح (رقم الطلب: {$newOrderId})",
                'order_id' => $newOrderId,
                'items_count' => $successCount
            ]);

        } catch (\Exception $e) {
            if (isset($this->itemOrderModel->db)) {
                $this->itemOrderModel->db->transRollback();
            }
            
            log_message('error', '❌ Error in sendReissueOrder: ' . $e->getMessage() . ' | Line: ' . $e->getLine() . ' | File: ' . $e->getFile());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في إرسال الطلب: ' . $e->getMessage()
            ]);
        }
    }
}