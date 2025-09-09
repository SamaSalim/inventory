<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Order extends Entity
{
    protected $attributes = [
        'order_id'=>null,
        'from_employee_id' => null,
        'to_employee_id' => null,
        'order_status_id' => null,
        'note' => null
    ];
}
