<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Employee;

class EmployeeModel extends Model
{
    protected $table            = 'employee';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Employee::class;
    protected $allowedFields    = ['emp_id', 'name', 'emp_dept', 'emp_ext', 'email', 'password'];
    protected $useTimestamps    = true;

    protected $validationRules = [
        'emp_id'   => 'required|is_unique[employee.emp_id]',
        'name'     => 'required|min_length[3]',
        'emp_dept' => 'required|max_length[255]',
        'emp_ext' => 'permit_empty|integer',
        'email'    => 'required|is_unique[employee.email]',
        'password' => 'required|min_length[6]',
    ];


    protected $validationMessages = [
        'emp_id'   => [
            'required' => "الرقم الوظيفي مطلوب",
            'is_unique' => 'الرقم الوظيفي مستخدم',
        ],
        'name'     => [
            'required' => 'الاسم مطلوب',
            'min_length' => "يجب ألا يقل الاسم عن {param} أحرف"
        ],
        'emp_dept' => [
            'required'   => 'القسم مطلوب',
            'max_length' => 'يجب ألا يزيد اسم القسم عن {param} أحرف',
        ],
        'emp_ext' => [
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


    // للتحقق من الباسوورد
    // protected $beforeInsert = ['hashPassword'];
    // protected $beforeUpdate = ['hashPassword'];

    // protected function hashPassword(array $data)
    // {
    // if (isset($data['data']['password'])) {
    //     $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
    // }
    // return $data;
    // }


}
