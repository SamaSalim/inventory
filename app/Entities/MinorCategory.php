<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;


class MinorCategory extends Entity
{

    protected $attributes = [
        'id'       => null,
        'name' => null,
        'major_category_id'=>null,

    ];
}
