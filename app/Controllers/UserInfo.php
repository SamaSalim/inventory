<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\PermissionModel;
use App\Models\RoleModel;
use App\Models\UserModel; 
use App\Entities\Employee;

class UserInfo extends BaseController
{
    public function getUserInfo()
    {
        $data = [];

        // حفظ عنوان URL للصفحة السابقة في الجلسة
        session()->setFlashdata('previous_url', previous_url());
        
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }


        $isEmployee = session()->get('isEmployee'); // جلب متغير التمييز من الجلسة
        $emp_id = session()->get('employee_id'); // هذا المتغير يحتوي على user_id أو emp_id

        $account = null;
        if ($isEmployee) {
            $employeeModel = new EmployeeModel();
            $account = $employeeModel->where('emp_id', $emp_id)->first();
        } else {
            $userModel = new UserModel();
            $account = $userModel->where('user_id', $emp_id)->first();
        }

        if (!$account) {
            $data['error_message'] = 'المستخدم غير موجود في النظام';
            return view('user/user_info', $data);
        }

        $permissionModel = new PermissionModel();

        // جلب الأدوار بناءً على نوع الحساب
        $permissions = null;
        if ($isEmployee) {
            $permissions = $permissionModel->select('role.*')
                ->join('role', 'role.id = permission.role_id')
                ->where('permission.emp_id', $emp_id)
                ->findAll();
        } else {
            $permissions = $permissionModel->select('role.*')
                ->join('role', 'role.id = permission.role_id')
                ->where('permission.user_id', $emp_id)
                ->findAll();
        }

        $roles = [];
        foreach ($permissions as $permission) {
            $roles[] = [
                'id' => $permission->id,
                'name' => $permission->name,
            ];
        }

        $account->roles = $roles;
        $data['account'] = $account;
        $data['user_roles_array'] = array_column($roles, 'name');
        $data['user_role'] = session()->get('role');

        return view('user/user_info', $data);
    }
}
