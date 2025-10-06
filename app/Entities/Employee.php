<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Employee extends Entity
{
    protected $attributes = [
        'id'       => null,
        'emp_id'   => null,
        'name'     => null,
        'emp_dept' => null, // added
        'emp_ext'    => null, // Added the missing field
        'email'    => null,
        'password' => null,
        'roles'    => [],
    ];


    public function setPassword(string $password)
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    public function setRoles(array $roles)
    {
        $this->attributes['roles'] = $roles;
        return $this;
    }
}
