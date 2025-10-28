<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class History extends Entity
{
    protected $attributes = [
        'id'              => null,
        'item_order_id'   => null,
        'usage_status_id' => null,
        'handled_by'      => null,
        'created_at'      => null,
        'updated_at'      => null,
    ];
}
