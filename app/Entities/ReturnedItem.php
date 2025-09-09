<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class ReturnedItem extends Entity
{
    protected $attributes = [
        'id'       => null,
        'item_order_id'=>null,
        'notes'=>null,
        'attach_id'=>null,
        'return_date'=>null


    ];
}
