<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class User extends Entity
{
    protected $attributes = [
        'id'       => null,
        'user_id'   => null,
        'name'     => null,
        'user_dept' => null, // added
        'user_ext'    => null, // Added the missing field
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
