<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class TransferItems extends Entity
{
    protected $attributes = [
        'transfer_item_id' => null,
        'order_id' => null,
        'from_user_id' => null,
        'to_user_id' => null,
        'order_status_id' => null,
        'is_opened' => 0, // 0 = لم يتم الفتح، 1 = تم الفتح
        'note' => null

    ];
}
