<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Room extends Entity
{
    protected $attributes = [
        'id'       => null,
        'code'   => null,
        'section_id' => null,

    ];
}
