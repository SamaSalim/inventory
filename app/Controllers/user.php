<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\PermissionModel;
use App\Models\RoleModel;
use App\Entities\Employee;

class user extends BaseController
{
    /**
     * Get user information by employee ID and display it in a view.
     * This function now correctly handles fetching all roles for an employee.
     */
 public function getUserInfo()
{ 
    $data = []; 

    // التحقق من تسجيل الدخول أولاً
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('/login')->with('error', 'يجب تسجيل الدخول أولاً');
    }

    $employeeModel = new EmployeeModel();
    $permissionModel = new PermissionModel();
    $roleModel = new RoleModel();

    // جلب emp_id من الجلسة بدلاً من الرابط
    $emp_id = session()->get('employee_id');
    $employee = $employeeModel->where('emp_id', $emp_id)->first();

    if (!$employee) {
        $data['error_message'] = 'الموظف غير موجود في النظام';
        return view('user_info', $data);
    }

    // جلب الأدوار مع تحسين الأداء
    $permissions = $permissionModel->select('role.*')
                                 ->join('role', 'role.id = permission.role_id')
                                 ->where('permission.emp_id', $emp_id)
                                 ->findAll();

    $roles = [];
    foreach ($permissions as $permission) {
        $roles[] = [
            'id' => $permission->id,
            'name' => $permission->name,
        ];
    }

    $employee->roles = $roles;
    $data['employee'] = $employee;

    return view('user_info', $data);
}
}