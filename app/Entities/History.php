<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class History extends Entity
{
    protected $attributes = [
        'id'       => null,
        'item_order_id' => null,
        'action' => null,
    ];
}
