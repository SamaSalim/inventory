<?php
// File: app/Controllers/WarehouseController.php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Entities\Item;
use App\Entities\ItemOrder;
use App\Exceptions\AuthenticationException;
use CodeIgniter\Database\Exceptions\DatabaseException;

class warehouse extends BaseController
{
    public function dashboard(): string
    {
        // exception handling
        if (! session()->get('isLoggedIn')) {
            throw new AuthenticationException();
        }

        return $this->index();
    }

    public function index()
    {
        // جلب بيانات item_order مع الربط مع الجداول المرتبطة
        $itemOrderModel = new \App\Models\ItemOrderModel();
        
        // جلب البيانات مع الربط (JOIN) مع الجداول الأخرى
        $itemOrders = $itemOrderModel->select('
                item_order.*, 
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
            ->orderBy('item_order.created_at', 'DESC')
            ->findAll();

        // جلب التصنيفات الفرعية
        $minorCategoryModel = new \App\Models\MinorCategoryModel();
        $categories = $minorCategoryModel->select('minor_category.*, major_category.name as major_category_name')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->findAll();

        // حساب الإحصائيات
        $stats = $this->getWarehouseStats();

        // جلب حالات الطلب وحالات الاستخدام
        $orderStatusModel = new \App\Models\OrderStatusModel();
        $usageStatusModel = new \App\Models\UsageStatusModel();
        $statuses = $orderStatusModel->findAll();
        $usageStatuses = $usageStatusModel->findAll();

        return view('warehouseView', [
            'categories' => $categories,
            'items' => $itemOrders, // إرسال بيانات item_order بدلاً من items
            'stats' => $stats,
            'statuses' => $statuses,
            'usage_statuses' => $usageStatuses,
        ]);
    }

    public function create()
    {
        // جلب التصنيفات مع الربط
        $minorCategoryModel = new \App\Models\MinorCategoryModel();
        $categories = $minorCategoryModel->select('minor_category.*, major_category.name as major_category_name')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->findAll();

        // جلب الأصناف المتاحة
        $itemModel = new ItemModel();
        $items = $itemModel->select('items.*, minor_category.name as category_name')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->findAll();

        // جلب الغرف مع معلومات كاملة
        $roomModel = new \App\Models\RoomModel();
        $rooms = $roomModel->select('
                room.*, 
                section.code as section_code,
                floor.code as floor_code,
                building.code as building_code
            ')
            ->join('section', 'section.id = room.section_id', 'left')
            ->join('floor', 'floor.id = section.floor_id', 'left')
            ->join('building', 'building.id = floor.building_id', 'left')
            ->findAll();

        // جلب حالات الاستخدام
        $usageStatusModel = new \App\Models\UsageStatusModel();
        $usageStatuses = $usageStatusModel->findAll();

        return view('addItemView', [
            'categories' => $categories,
            'items' => $items,
            'rooms' => $rooms,
            'usage_statuses' => $usageStatuses
        ]);
    }

    public function addNewItem()
    {
        $postData = $this->request->getPost();
        $postData['created_by'] = session()->get('emp_id');
        $itemType = $this->request->getPost('item_type');

        try {
            if ($itemType === 'new_item') {
                // إضافة عنصر جديد إلى جدول items
                $itemEntity = new Item([
                    'name' => $postData['name'],
                    'minor_category_id' => $postData['minor_category_id']
                ]);

                $itemModel = new ItemModel();
                $itemModel->insert($itemEntity);

                return redirect()->to('/warehouse')->with('success', 'تمت إضافة الصنف الجديد بنجاح');

            } else {
                // إضافة طلب عنصر إلى جدول item_order
                $itemOrderEntity = new ItemOrder([
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

                $itemOrderModel = new \App\Models\ItemOrderModel();
                $itemOrderModel->insert($itemOrderEntity);

                return redirect()->to('/warehouse')->with('success', 'تمت إضافة طلب الصنف بنجاح');
            }

        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            log_message('error', 'Database Exception in addNewItem: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ في قاعدة البيانات: ' . $e->getMessage());
        } catch (\Exception $e) {
            log_message('error', 'General Exception in addNewItem: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ غير متوقع');
        }
    }

    public function edit($id)
    {
        $itemModel = new ItemModel();
        $minorCategoryModel = new \App\Models\MinorCategoryModel();
        
        $data['item'] = $itemModel->find($id);
        $data['categories'] = $minorCategoryModel->select('minor_category.*, major_category.name as major_category_name')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->findAll();

        return view('editItemView', $data);
    }

    public function update($id)
    {
        $itemModel = new \App\Models\ItemModel();
        $postData = $this->request->getPost();

        $entity = new Item($postData);
        $entity->id = $id;

        $itemModel->save($entity);

        return redirect()->to('/warehouse')->with('success', 'تم تحديث العنصر بنجاح');
    }

    private function getEmployeeNameById($emp_id)
    {
        $employeeModel = new \App\Models\EmployeeModel();
        $employee = $employeeModel->where('emp_id', $emp_id)->first();
        return $employee ? $employee->name : 'غير محدد';
    }

    /**
     * حساب إحصائيات المستودع المحدثة
     */
    private function getWarehouseStats(): array
    {
        $itemOrderModel = new \App\Models\ItemOrderModel();
        $itemModel = new ItemModel();

        // 1. إجمالي الكميات المستلمة
        $totalQuantityResult = $itemOrderModel->selectSum('quantity')->first();
        $totalReceipts = $totalQuantityResult ? (int)$totalQuantityResult->quantity : 0;

        // 2. عدد الأصناف المتوفرة
        $availableItems = $itemOrderModel->countAllResults();

        // 3. إجمالي الإدخالات في جدول items
        $totalEntries = $itemModel->countAllResults();

        // 4. الأصناف منخفضة المخزون
        $lowStock = $itemOrderModel->where('quantity <', 10)
            ->where('quantity >', 0)
            ->countAllResults();

        // 5. أكثر التصنيفات استخداماً
        $topCategoryResult = $itemOrderModel->select('items.minor_category_id, minor_category.name, COUNT(*) as count')
            ->join('items', 'items.id = item_order.item_id')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->groupBy('items.minor_category_id')
            ->orderBy('count', 'DESC')
            ->first();
        
        $topCategory = $topCategoryResult ? $topCategoryResult->name : 'غير محدد';

        // 6. آخر عملية إضافة
        $lastEntry = $itemOrderModel->select('item_order.created_at, items.name')
            ->join('items', 'items.id = item_order.item_id', 'left')
            ->orderBy('item_order.created_at', 'DESC')
            ->first();

        return [
            'total_receipts' => $totalReceipts,
            'available_items' => $availableItems,
            'total_entries' => $totalEntries,
            'low_stock' => $lowStock,
            'top_category' => $topCategory,
            'last_entry' => $lastEntry ? [
                'item' => $lastEntry->name ?? 'غير محدد',
                'date' => date('Y-m-d H:i', strtotime($lastEntry->created_at))
            ] : null
        ];
    }

    /**
     * API endpoint لإرجاع الإحصائيات كـ JSON
     */
    public function getStats()
    {
        $stats = $this->getWarehouseStats();

        return $this->response->setJSON([
            'total_receipts' => $stats['total_receipts'],
            'available_items' => $stats['available_items'],
            'total_entries' => $stats['total_entries'],
            'low_stock' => $stats['low_stock']
        ]);
    }

    /**
     * إحصائيات متقدمة للتقارير
     */
    public function getAdvancedStats()
    {
        $itemOrderModel = new \App\Models\ItemOrderModel();

        // توزيع الأصناف حسب التصنيف الفرعي
        $categoryDistribution = $itemOrderModel->select('
                items.minor_category_id, 
                minor_category.name as category_name,
                COUNT(*) as count, 
                SUM(item_order.quantity) as total_qty
            ')
            ->join('items', 'items.id = item_order.item_id')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->groupBy('items.minor_category_id')
            ->orderBy('count', 'DESC')
            ->findAll();

        // الأصناف الأكثر كمية
        $topQuantities = $itemOrderModel->select('
                items.name, 
                item_order.quantity, 
                minor_category.name as category_name
            ')
            ->join('items', 'items.id = item_order.item_id')
            ->join('minor_category', 'minor_category.id = items.minor_category_id', 'left')
            ->orderBy('item_order.quantity', 'DESC')
            ->limit(5)
            ->findAll();

        // معدل الإدخالات الشهرية
        $monthlyEntries = $itemOrderModel->select("
                DATE_FORMAT(created_at, '%Y-%m') as month, 
                COUNT(*) as entries
            ")
            ->where('created_at >=', date('Y-01-01'))
            ->groupBy("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderBy('month', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'category_distribution' => $categoryDistribution,
            'top_quantities' => $topQuantities,
            'monthly_entries' => $monthlyEntries
        ]);
    }
}