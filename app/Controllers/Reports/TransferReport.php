<?php

namespace App\Controllers\Reports;

use App\Controllers\BaseController;
use App\Models\TransferItemsModel;

class TransferReport extends BaseController
{
    private TransferItemsModel $transferModel;

    public function __construct()
    {
        $this->transferModel = new TransferItemsModel();
    }

    /**
     * Print single transfer report by asset number
     */
    public function printSingleTransfer($assetNumber = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        if (!$assetNumber) {
            return redirect()->back()->with('error', 'رقم الأصل غير موجود');
        }

        // Get transfer data for this specific asset
        $transfer = $this->transferModel
            ->select('
                transfer_items.*,
                item_order.asset_num,
                item_order.old_asset_num,
                item_order.brand,
                item_order.model_num,
                items.name as item_name,
                usage_status.usage_status,
                from_user.name as from_user_name,
                to_user.name as to_user_name
            ')
            ->join('item_order', 'item_order.item_order_id = transfer_items.item_order_id')
            ->join('items', 'items.id = item_order.item_id')
            ->join('usage_status', 'usage_status.id = item_order.usage_status_id')
            ->join('users as from_user', 'from_user.user_id = transfer_items.from_user_id', 'left')
            ->join('users as to_user', 'to_user.user_id = transfer_items.to_user_id', 'left')
            ->where('item_order.asset_num', $assetNumber)
            ->orderBy('transfer_items.created_at', 'DESC')
            ->first();

        if (!$transfer) {
            return redirect()->back()->with('error', 'لم يتم العثور على بيانات التحويل لهذا الأصل');
        }

        // Prepare data for view
        $items = [[
            'asset_num' => $transfer->asset_num,
            'old_asset_num' => $transfer->old_asset_num ?? '',
            'item_name' => $transfer->item_name,
            'brand' => $transfer->brand ?? '',
            'model' => $transfer->model_num ?? '',
            'usage_status' => $transfer->usage_status ?? '',
            'notes' => $transfer->note ?? ''
        ]];

        $data = [
            'items' => $items,
            'from_user_name' => $transfer->from_user_name ?? 'غير معروف',
            'to_user_name' => $transfer->to_user_name ?? 'غير معروف',
            'current_date' => date('Y-m-d')
        ];

        return view('assets/reports/show_transfer', $data);
    }
}