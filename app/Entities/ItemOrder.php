<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class ItemOrder extends Entity
{
    protected $attributes = [
        'item_order_id' => null,
        'order_id' => null,
        'item_id' => null,
        'brand' => null,
        'quantity' => null,
        'model_num' => null,
        'asset_num' => null,
        'serial_num' => null,
        'old_asset_num' => null,
        'room_id' => null,
        'created_by' => null,
        'usage_status_id' => null,
        'assets_type' => 'غير محدد',
        'attachment' => null,
        'note' => null
    ];
}
