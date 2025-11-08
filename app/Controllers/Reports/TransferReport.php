<?php

namespace App\Controllers\Reports;

use App\Controllers\BaseController;
use App\Models\TransferItemsModel;
use App\Models\ItemOrderModel;
use App\Models\ItemModel;
use App\Models\MinorCategoryModel;
use App\Models\MajorCategoryModel;
use App\Models\UserModel;
use App\Models\RoomModel;
use App\Models\UsageStatusModel;

class TransferReport extends BaseController
{
    private TransferItemsModel $model;

    public function __construct()
    {
        $this->model = new TransferItemsModel();
    }

    public function index()
    {
        // Get user role
        $userRole = session()->get('role');
        $userId = session()->get('employee_id') ?? session()->get('user_id');

        // Get filter parameters
        $assetNumber = $this->request->getGet('asset_number');
        $itemName = $this->request->getGet('item_name');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        $toUserId = $this->request->getGet('returned_by');

        if (!in_array($userRole, ['assets', 'super_assets'])) {
            $toUserId = $userId;
        }

        $builder = $this->model
            ->select('
                transfer_items.transfer_item_id,
                transfer_items.from_user_id,
                transfer_items.to_user_id,
                transfer_items.note,
                transfer_items.created_at,
                item_order.asset_num,
                item_order.serial_num,
                item_order.model_num,
                item_order.old_asset_num,
                item_order.brand,
                item_order.assets_type,
                item_order.room_id,
                item_order.usage_status_id,
                item_order.order_id,
                items.name as item_name,
                minor_category.name as minor_category,
                major_category.name as major_category,
                usage_status.usage_status as usage_status_name,
                from_user.name as from_user_name,
                to_user.name as to_user_name
            ')
            ->join('item_order', 'item_order.item_order_id = transfer_items.item_order_id')
            ->join('items', 'items.id = item_order.item_id')
            ->join('minor_category', 'minor_category.id = items.minor_category_id')
            ->join('major_category', 'major_category.id = minor_category.major_category_id')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id')
            ->join('users as from_user', 'from_user.user_id = transfer_items.from_user_id', 'left')
            ->join('users as to_user', 'to_user.user_id = transfer_items.to_user_id', 'left');

        // Apply filters
        if (!empty($assetNumber)) {
            $builder->like('item_order.asset_num', $assetNumber);
        }

        if (!empty($itemName)) {
            $builder->like('items.name', $itemName);
        }

        if (!empty($dateFrom)) {
            $builder->where('DATE(transfer_items.created_at) >=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $builder->where('DATE(transfer_items.created_at) <=', $dateTo);
        }

        if (!empty($toUserId)) {
            $builder->where('transfer_items.to_user_id', $toUserId);
        }

        $transfers = $builder->orderBy('transfer_items.created_at', 'DESC')->findAll();

        // Process transfers
        $roomModel = new RoomModel();
        $processedItems = [];
        
        foreach ($transfers as $transfer) {
            $locationCode = $roomModel->getFullLocationCode($transfer->room_id);
            
            $processedItems[] = [
                'transfer_item_id' => $transfer->transfer_item_id,
                'order_id' => $transfer->order_id,
                'asset_num' => $transfer->asset_num,
                'serial_num' => $transfer->serial_num ?? '-',
                'item_name' => $transfer->item_name,
                'minor_category' => $transfer->minor_category,
                'major_category' => $transfer->major_category,
                'asset_type' => $transfer->assets_type ?? '-',
                'brand' => $transfer->brand ?? '-',
                'model' => $transfer->model_num ?? '-',
                'old_asset_num' => $transfer->old_asset_num ?? '-',
                'location_code' => $locationCode ?? '-',
                'usage_status' => $transfer->usage_status_name ?? 'غير محدد',
                'created_at' => $transfer->created_at,
                'notes' => $transfer->note ?? '',
                'from_user_id' => $transfer->from_user_id,
                'from_user_name' => $transfer->from_user_name ?? 'غير محدد',
                'to_user_id' => $transfer->to_user_id,
                'to_user_name' => $transfer->to_user_name ?? 'غير محدد'
            ];
        }

        // Get sender and recipient names from first item
        $fromUserName = 'غير معروف';
        $fromUserId = 'غير محدد';
        $toUserName = 'غير معروف';
        $toUserIdValue = 'غير محدد';
        
        if (!empty($processedItems)) {
            $fromUserName = $processedItems[0]['from_user_name'];
            $fromUserId = $processedItems[0]['from_user_id'];
            $toUserName = $processedItems[0]['to_user_name'];
            $toUserIdValue = $processedItems[0]['to_user_id'];
        }

        $data = [
            'items' => $processedItems,
            'from_user_name' => $fromUserName,
            'from_user_id' => $fromUserId,
            'to_user_name' => $toUserName,
            'to_user_id' => $toUserIdValue,
            'total_count' => count($processedItems),
            'current_date' => date('Y-m-d'),
            'filters' => [
                'asset_number' => $assetNumber,
                'item_name' => $itemName,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'returned_by' => $toUserId
            ]
        ];

        return view('assets/reports/show_transfer', $data);
    }
}