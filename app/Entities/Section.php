<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Section extends Entity
{
    protected $attributes = [
        'id'       => null,
        'code'   => null,
        'floor_id' => null,

    ];
}
