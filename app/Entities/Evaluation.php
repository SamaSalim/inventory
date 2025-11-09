<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Evaluation  extends Entity
{
    protected $attributes = [
        'id'       => null,
        'file_name' => null,
        'file_path' => null

    ];
}
