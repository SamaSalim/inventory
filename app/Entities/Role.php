<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Role extends Entity
{
    protected $attributes = [
        'id'   => null,
        'name' => null,
    ];
}
