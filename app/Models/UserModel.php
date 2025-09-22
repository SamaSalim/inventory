<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\User;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = User::class;
    protected $allowedFields    = ['user_id', 'name', 'user_dept', 'user_ext', 'email', 'password'];
    protected $useTimestamps    = true;

    protected $validationRules = [
        'user_id'   => 'required|is_unique[employee.emp_id]',
        'name'     => 'required|min_length[3]',
        'user_dept' => 'required|max_length[255]',
        'user_ext' => 'permit_empty|integer',
        'email'    => 'required|is_unique[employee.email]',
        'password' => 'required|min_length[6]',
    ];


    protected $validationMessages = [
        'user_id'   => [
            'required' => "الرقم الوظيفي مطلوب",
            'is_unique' => 'الرقم الوظيفي مستخدم',
        ],
        'name'     => [
            'required' => 'الاسم مطلوب',
            'min_length' => "يجب ألا يقل الاسم عن {param} أحرف"
        ],
        'user_dept' => [
            'required'   => 'القسم مطلوب',
            'max_length' => 'يجب ألا يزيد اسم القسم عن {param} أحرف',
        ],
        'user_ext' => [
            'integer' => 'رقم التحويلة يجب أن يكون رقم صحيح'
        ],
        'email'    => [
            'required' => 'الإيميل مطلوب',
            'is_unique' => 'الايميل مستخدم'
        ],
        'password' => [
            'required' => 'كلمة المرور مطلوبة',
            'min_length' => "يجب ألا تقل كلمة المرور عن {param} أحرف"
        ]
    ];
}
