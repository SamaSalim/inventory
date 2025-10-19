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
        if (! session()->get('isLoggedIn')) {
            throw new \CodeIgniter\Shield\Exceptions\AuthenticationException();
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
               ->where('usage_status_id !=', 2);
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
                    ->where('usage_status_id !=', 2)
                    ->findAll();

        foreach ($items as $item) {
            $itemData = $itemModel->find($item->item_id);
            $minor    = $itemData ? $minorCatModel->find($itemData->minor_category_id) : null;
            $major    = $minor ? $majorCatModel->find($minor->major_category_id) : null;

            $item->item_name             = $itemData->name ?? 'غير معروف';
            $item->minor_category_name  = $minor->name ?? 'غير معروف';
            $item->major_category_name  = $major->name ?? 'غير معروف';
            $item->location_code        = $roomModel->getFullLocationCode($item->room_id);
            $item->usage_status_name    = $usageStatusModel->find($item->usage_status_id)->usage_status ?? 'غير معروف';
            $item->created_by_name      = $employeeModel->where('emp_id', $item->created_by)->first()->name ?? 'غير معروف';
        }

        return view('assets/return_order', [
            'order'       => $order,
            'items'       => $items,
            'item_count'  => count($items),
        ]);
    }


// اكواد تحويل العهدة
public function transferView($orderId)
{
    if (!session()->get('isLoggedIn')) {
        throw new \CodeIgniter\Shield\Exceptions\AuthenticationException();
    }

    $itemOrderModel = new \App\Models\ItemOrderModel();
    $itemModel = new \App\Models\ItemModel();
    $minorCatModel = new \App\Models\MinorCategoryModel();
    $majorCatModel = new \App\Models\MajorCategoryModel();
    $usageStatusModel = new \App\Models\UsageStatusModel();
    $userModel = new \App\Models\UserModel();
    $orderModel = new \App\Models\OrderModel(); // إضافة OrderModel
    
    // جلب بيانات العهدة
    $order = $orderModel->find($orderId);
    
    if (!$order) {
        throw new \Exception('العهدة غير موجودة');
    }

    // جلب الأصول المرتبطة بالطلب والتي ليست مرجعة
    $items = $itemOrderModel
        ->where('order_id', $orderId)
        ->where('usage_status_id !=', 2) // ليست مرجعة
        ->findAll();

    foreach ($items as $item) {
        $itemData = $itemModel->find($item->item_id);
        $minor = $itemData ? $minorCatModel->find($itemData->minor_category_id) : null;
        $major = $minor ? $majorCatModel->find($minor->major_category_id) : null;

        
        $item->item_name = $itemData->name ?? 'غير معروف';
        $item->minor_category_name = $minor->name ?? 'غير معروف';
        $item->major_category_name = $major->name ?? 'غير معروف';
        $item->usage_status_name = $usageStatusModel->find($item->usage_status_id)->usage_status ?? 'غير معروف';
    }

    // جلب جميع المستخدمين
    $users = $userModel->findAll();

    return view('assets/transfer_order', [
        'items' => $items,
        'users' => $users,
        'order_id' => $orderId,
        'order' => $order // إضافة بيانات العهدة
    ]);
}

/**
 * معالجة طلب التحويل
 */
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
        $firstItemOrderId = null; // لحفظ أول item_order_id
        
        // معالجة كل أصل محدد
        foreach ($itemOrderIds as $itemOrderId) {
            // حفظ أول item_order_id
            if ($firstItemOrderId === null) {
                $firstItemOrderId = $itemOrderId;
            }
            
            $currentItem = $itemOrderModel
                ->select('item_order.*, items.name as item_name')
                ->join('items', 'items.id = item_order.item_id')
                ->find($itemOrderId);
            
            if (!$currentItem) {
                log_message('warning', "Item order {$itemOrderId} not found");
                continue;
            }

            $itemsDetails[] = $currentItem;

            // تحديث حالة الأصل إلى "قيد التحويل"
            $itemOrderModel->update($itemOrderId, [
                'usage_status_id' => 5,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // إضافة سجل في جدول transfer_items
            $transferData = [
                'item_order_id' => $itemOrderId,
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
                'order_status_id' => 1, // قيد الانتظار
                'note' => $note,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $transferItemsModel->insert($transferData);
        }

        // Send email notification مع أول item_order_id
        $this->sendTransferEmail($toUser, $fromUser, $itemsDetails, $note, $firstItemOrderId);

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
}