<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Items extends Entity
{
    protected $attributes = [
        'id' => null,
        'name'=>null,
        'minor_category_id' => null, // اضفتة

    ];
}
