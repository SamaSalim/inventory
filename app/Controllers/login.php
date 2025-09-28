<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\PermissionModel;
use App\Models\RoleModel;
use App\Models\UserModel; 

class login extends BaseController
{
    public function index(): string
    {
        return view('login_form');
    }

    public function login()
    {
        $empID = $this->request->getPost('emp_id');
        $password = $this->request->getPost('password');

        $employeeModel = new EmployeeModel();
        $userModel = new UserModel(); 
        $permissionModel = new PermissionModel();
        $roleModel = new RoleModel();

        // 1. محاولة البحث عن المستخدم في جدول 'employee'
        $account = $employeeModel->where('emp_id', $empID)->first();
        $isEmployee = true;

        // إذا لم يتم العثور عليه، حاول البحث في جدول 'users'
        if (!$account) {
            $account = $userModel->where('user_id', $empID)->first();
            $isEmployee = false;
        }

        // التحقق من وجود الحساب وتطابق كلمة المرور
        if (!$account || !password_verify($password, $account->password)) {
            return redirect()->back()->with('error', 'الرقم الوظيفي أو كلمة المرور غير صحيحة.');
        }

        // جلب الصلاحيات بناءً على نوع الحساب
        $permission = null;
        if ($isEmployee) {
            $permission = $permissionModel->where('emp_id', $account->emp_id)->first();
        } else {
            $permission = $permissionModel->where('user_id', $account->user_id)->first();
        }

        if (!$permission || empty($permission->role_id)) {
            return redirect()->back()->with('error', 'لا يوجد دور مخصص أو بيانات صلاحيات غير صالحة.');
        }

        $role = $roleModel->find($permission->role_id);

        if (!$role) {
            return redirect()->back()->with('error', 'الدور غير صالح.');
        }

        // تعيين بيانات الجلسة
        session()->set([
            'employee_id' => $isEmployee ? $account->emp_id : $account->user_id, // استخدام المفتاح الصحيح
            'name'        => $account->name,
            'role'        => $role->name,
            'isLoggedIn'  => true,
            'isEmployee'  => $isEmployee, // إضافة متغير لتمييز نوع الحساب
        ]);

        // التوجيه بناءً على الدور
        if ($role->name === 'warehouse') {
            return redirect()->to('/InventoryController/index');
        } elseif ($role->name === 'assets') {
            return redirect()->to('/AssetsController/dashboard');
        } elseif ($role->name === 'admin') {
            return redirect()->to('/AdminController/dashboard');
        } elseif ($role->name === 'user') {
            return redirect()->to('/UserController/dashboard');
        } else {
            return redirect()->back()->with('error', 'دور غير معروف.');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
