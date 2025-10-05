<?php

namespace App\Controllers;

use App\Models\ItemOrderModel;
use App\Models\MinorCategoryModel;
use App\Models\UsageStatusModel;
use App\Exceptions\AuthenticationException;

class AssetsHistory extends BaseController
{
    /**
     * دالة خاصة للتحقق من تسجيل الدخول
     */
    private function checkAuth()
    {
        if (! session()->get('isLoggedIn')) {
            throw new AuthenticationException();
        }
    }

    /**
     * عرض صفحة return_view مع بيانات الأصول مع فلترة
     */
    public function index(): string
    {
        $this->checkAuth(); // تحقق من تسجيل الدخول

        $itemOrderModel = new ItemOrderModel();

        // الحصول على قيم الفلترة من الطلب (GET أو POST)
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $usageStatusId = $this->request->getGet('usage_status_id');

        // بناء الاستعلام
        $itemOrdersQuery = $itemOrderModel
            ->distinct()
            ->select(
                'item_order.order_id, 
                 item_order.created_at, 
                 item_order.created_by, 
                 room.code AS room_code, 
                 employee.name AS created_by_name, 
                 employee.emp_id AS employee_id, 
                 employee.emp_ext AS extension,
                 usage_status.usage_status AS usage_status_name'
            )
            ->join('employee', 'employee.emp_id = item_order.created_by', 'left')
            ->join('room', 'room.id = item_order.room_id', 'left')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id', 'left');

        // تطبيق الفلترة حسب التاريخ إذا موجودة
        if ($startDate) {
            $itemOrdersQuery->where('item_order.created_at >=', $startDate . ' 00:00:00');
        }
        if ($endDate) {
            $itemOrdersQuery->where('item_order.created_at <=', $endDate . ' 23:59:59');
        }

        // فلترة حسب حالة الاستخدام إذا محددة
        if ($usageStatusId) {
            $itemOrdersQuery->where('item_order.usage_status_id', $usageStatusId);
        }

        // ترتيب البيانات
        $itemOrders = $itemOrdersQuery->orderBy('item_order.created_at', 'DESC')->findAll();

        // جلب الفئات
        $minorCategoryModel = new MinorCategoryModel();
        $categories = $minorCategoryModel->select('minor_category.*, major_category.name AS major_category_name')
            ->join('major_category', 'major_category.id = minor_category.major_category_id', 'left')
            ->findAll();

        // حالات الاستخدام
        $usageStatusModel = new UsageStatusModel();
        $usageStatuses = $usageStatusModel->findAll();

        // تمرير البيانات للفيو
        return view('assets/return_view', [
            'categories' => $categories,
            'orders' => $itemOrders,
            'usage_statuses' => $usageStatuses,
            'filter' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'usage_status_id' => $usageStatusId
            ]
        ]);
    }
}
