<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Order extends Entity
{
    protected $attributes = [
        'order_id'=>null,
        'from_user_id' => null,
        'to_user_id' => null,
        'order_status_id' => null,
        'note' => null
    ];
}
