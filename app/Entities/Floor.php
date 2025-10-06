<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Floor extends Entity
{
    protected $attributes = [
        'id'       => null,
        'code'   => null,
        'building_id' => null,

    ];
}
