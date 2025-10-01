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
};
use CodeIgniter\HTTP\ResponseInterface;
use App\Exceptions\AuthenticationException;



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

        // فلترة البحث
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

        $itemOrders = $builder->paginate(5, 'orders');
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
    return view('assets/transfer_order', [
        'order'       => $order,
        'items'       => $items,
        'item_count'  => count($items),
    ]);
}
public function processReturnWithFiles()
{
    try {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً'
            ]);
        }

        $loggedEmployeeId = session()->get('employee_id');
        $assetNums = $this->request->getPost('asset_nums');
        $comments  = $this->request->getPost('comments');

        if (empty($assetNums) || !is_array($assetNums)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'لم يتم تحديد أي عناصر للترجيع'
            ]);
        }

        $returnedStatus = $this->usageStatusModel->where('usage_status', 'رجيع')->first();
        if (!$returnedStatus) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'حالة "رجيع" غير موجودة في النظام'
            ]);
        }

        $uploadPath = WRITEPATH . 'uploads/return_attachments';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $successCount = 0;
        $failedItems  = [];
        $allFiles = $this->request->getFiles();

        foreach ($assetNums as $assetNum) {
            $originalItem = $this->itemOrderModel->where('asset_num', $assetNum)->first();
            if (!$originalItem) {
                $failedItems[] = "الأصل رقم: $assetNum";
                continue;
            }

            // === FILE UPLOAD HANDLING ===
            $uploadedFileNames = [];
            if (isset($allFiles['attachments'][$assetNum])) {
                foreach ($allFiles['attachments'][$assetNum] as $file) {
                    if (!$file->isValid()) continue;

                    if ($file->getSizeByUnit("mb") > 5) {
                        $failedItems[] = "ملف كبير جداً للأصل: $assetNum - " . $file->getName();
                        continue;
                    }

                    $allowedMimes = [
                        "image/png", "image/jpeg", "image/jpg",
                        "application/pdf",
                        "application/msword",
                        "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                    ];

                    if (!in_array($file->getMimeType(), $allowedMimes)) {
                        $failedItems[] = "نوع ملف غير مسموح للأصل: $assetNum - " . $file->getName();
                        continue;
                    }

                    $newName = $assetNum . '_' . time() . '_' . $file->getRandomName();
                    if ($file->move($uploadPath, $newName)) {
                        $uploadedFileNames[] = $newName;
                    }
                }
            }

            $attachmentPath = !empty($uploadedFileNames) ? implode(',', $uploadedFileNames) : $originalItem->attachment;

            // === UPDATE EXISTING RECORD ===
            $updateData = [
                'created_by'      => $loggedEmployeeId,
                'usage_status_id' => $returnedStatus->id, // ID = 2
                'note'            => $comments[$assetNum] ?? 'تم الترجيع',
                'attachment'      => $attachmentPath,
                'updated_at'      => date('Y-m-d H:i:s')
            ];

            $updated = $this->itemOrderModel->update($originalItem->item_order_id, $updateData);

            if ($updated) {
                $successCount++;
            } else {
                $failedItems[] = "الأصل رقم: $assetNum";
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'فشل في حفظ البيانات'
            ]);
        }

        $message = "تم تحديث $successCount عنصر بنجاح";
        if (!empty($failedItems)) {
            $message .= "\n\nفشل التحديث: " . implode(', ', $failedItems);
        }

        return $this->response->setJSON([
            'success'        => true,
            'message'        => $message,
            'updated_count'  => $successCount,
            'failed_count'   => count($failedItems)
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Error in processReturnWithFiles: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'خطأ في معالجة الترجيع: ' . $e->getMessage()
        ]);
    }
}


}