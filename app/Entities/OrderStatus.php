<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;


class OrderStatus extends Entity
{

    protected $attributes = [
        'id'       => null,
        'status' => null,

    ];
}
