<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Permission extends Entity
{
    protected $attributes = [
        'id'          => null,
        'emp_id'      => null,
        'user_id'      => null,
        'role_id'     => null,
    ];
}
