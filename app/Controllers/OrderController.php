<?php

namespace App\Controllers;

use App\Services\OrderService;

class OrderController extends BaseController
{
    protected $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    /**
     * عرض صفحة إضافة طلب متعدد الأصناف
     */
    public function index()
    {
        return $this->orderService->showOrderPage();
    }

    /**
     * عرض صفحة تعديل الطلب
     */
    public function editOrder($orderId = null)
    {
        return $this->orderService->showEditOrderPage($orderId);
    }

    /**
     * حفظ الطلب متعدد الأصناف
     */
    public function storeMultiItem()
    {
        try {
            return $this->orderService->storeMultiItem($this->request);
        } catch (\Exception $e) {
            log_message('error', 'Error in storeMultiItem controller: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في حفظ الطلب: ' . $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    /**
     * جلب بيانات الطلب للتعديل
     */
    public function getOrderData($orderId = null)
    {
        return $this->orderService->getOrderData($orderId);
    }

    /**
     * تحديث الطلب متعدد الأصناف
     */
    public function updateMultiItem($orderId = null)
    {
        try {
            return $this->orderService->updateMultiItem($orderId, $this->request);
        } catch (\Exception $e) {
            log_message('error', 'Error in updateMultiItem controller: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'خطأ في تحديث الطلب: ' . $e->getMessage()
            ])->setStatusCode(400);
        }
    }

    /**
     * البحث عن الأصناف مع التصنيفات
     */
    public function searchItems()
    {
        return $this->orderService->searchItems($this->request);
    }

    /**
     * البحث عن المستخدمين (Users)
     */
    public function searchUser()
    {
        return $this->orderService->searchUser($this->request);
    }

    /**
     * الحصول على بيانات النموذج الأولية (المباني و custody types)
     */
    public function getFormData()
    {
        return $this->orderService->getFormData();
    }

    /**
     * الحصول على الطوابق حسب المبنى
     */
    public function getFloorsByBuilding($buildingId = null)
    {
        return $this->orderService->getFloorsByBuilding($buildingId);
    }

    /**
     * الحصول على الأقسام حسب الطابق
     */
    public function getSectionsByFloor($floorId = null)
    {
        return $this->orderService->getSectionsByFloor($floorId);
    }

    /**
     * الحصول على الغرف حسب القسم
     */
    public function getRoomsBySection($sectionId = null)
    {
        return $this->orderService->getRoomsBySection($sectionId);
    }

    /**
     * التحقق من تكرار أرقام الأصولة
     */
    public function validateAssetSerial()
    {
        return $this->orderService->validateAssetSerial($this->request);
    }
}
