<?php

namespace App\Controllers\Return;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\ItemOrderModel;
use App\Models\UserModel;
use App\Models\ItemModel;
use App\Models\MinorCategoryModel;
use App\Models\MajorCategoryModel;
use App\Models\RoomModel;
use App\Models\UsageStatusModel;
use App\Models\EmployeeModel;
use App\Models\OrderStatusModel;

class RequestHandler extends BaseController
{
    protected $orderModel;
    protected $itemOrderModel;

    public function __construct()
    {
        $this->orderModel     = new OrderModel();
        $this->itemOrderModel = new ItemOrderModel();
    }

    /**
     * عرض صفحة إرجاع الأصول لطلب محدد
     */
    public function showReturnAssets($orderId)
    {
        $order = $this->orderModel->find($orderId);

        if (!$order) {
            return redirect()->back()->with('error', 'الطلب غير موجود');
        }

        // جلب أسماء المستخدمين والحالة
        $userModel      = new UserModel();
        $statusModel    = new OrderStatusModel();
        $fromUser       = $userModel->find($order->from_user_id);
        $toUser         = $userModel->find($order->to_user_id);
        $status         = $statusModel->find($order->order_status_id);

        $order->from_name   = $fromUser->name ?? 'غير معروف';
        $order->to_name     = $toUser->name ?? 'غير معروف';
        $order->status_name = $status->status ?? 'غير معروف';

        // جلب العناصر
        $items = $this->itemOrderModel->where('order_id', $orderId)->findAll();

        // إضافة معلومات إضافية لكل عنصر
        $itemModel       = new ItemModel();
        $minorCatModel   = new MinorCategoryModel();
        $majorCatModel   = new MajorCategoryModel();
        $roomModel       = new RoomModel();
        $usageStatusModel = new UsageStatusModel();
        $employeeModel    = new EmployeeModel();

        foreach ($items as $item) {
            $itemData = $itemModel->find($item->item_id);
            $minor    = $itemData ? $minorCatModel->find($itemData->minor_category_id) : null;
            $major    = $minor ? $majorCatModel->find($minor->major_category_id) : null;

            $item->item_name            = $itemData->name ?? 'غير معروف';
            $item->minor_category_name  = $minor->name ?? 'غير معروف';
            $item->major_category_name  = $major->name ?? 'غير معروف';
            $item->location_code        = $roomModel->getFullLocationCode($item->room_id);
            $item->usage_status_name    = $usageStatusModel->find($item->usage_status_id)->usage_status ?? 'غير معروف';
            $item->created_by_name      = $employeeModel->find($item->created_by)->name ?? 'غير معروف';
        }

        $item_count = count($items);

        return view('assets/return_assets', [
            'order'      => $order,
            'items'      => $items,
            'item_count' => $item_count,
        ]);
    }
}
