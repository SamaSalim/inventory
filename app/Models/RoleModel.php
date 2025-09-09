<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Role;

class RoleModel extends Model
{
    protected $table         = 'role';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;
    protected $returnType    = Role::class;
    protected $allowedFields = ['name'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'name' => 'required|max_length[100]|is_unique[role.name,id,{id}]'
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'اسم الدور مطلوب',
            'max_length' => 'اسم الدور لا يمكن أن يزيد عن 100 حرف',
            'is_unique' => 'اسم الدور موجود مسبقاً'
        ]
    ];
}
