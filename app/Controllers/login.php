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
            // البحث مازال يعتمد على user_id الموجود في جدول users
            $account = $userModel->where('user_id', $empID)->first();
            $isEmployee = false;
        }

        // التحقق من وجود الحساب وتطابق كلمة المرور
        if (!$account || !password_verify($password, $account->password)) {
            return redirect()->back()->with('error', 'الرقم الوظيفي أو كلمة المرور غير صحيحة.');
        }

        // جلب الصلاحيات بناءً على نوع الحساب
        $permission = null;
        $roleName = null; // الدور سيكون null حتى يتم تعيينه

        if ($isEmployee) {
            // للموظف: لا يزال البحث عن الصلاحيات مطلوباً
            $permission = $permissionModel->where('emp_id', $account->emp_id)->first();
        } else {
            // ويُعطى الدور الافتراضي 'user' مباشرة.
            $roleName = 'user';
        }
        
        // جزء التحقق من الصلاحية وجلب الدور: يتم تنفيذه فقط للموظفين
        if ($isEmployee) {
            if (!$permission || empty($permission->role_id)) {
                 return redirect()->back()->with('error', 'لا يوجد دور مخصص أو بيانات صلاحيات غير صالحة للموظف.');
            }

            $role = $roleModel->find($permission->role_id);

            if (!$role) {
                return redirect()->back()->with('error', 'الدور غير صالح.');
            }
            
            $roleName = $role->name; // تحديث الدور من قاعدة البيانات
        }
        
        // **ملاحظة:** إذا كان $isEmployee خاطئ، فإن $roleName هو 'user' بالفعل من خطوة التعديل أعلاه.

        // تعيين بيانات الجلسة
        session()->set([
            // استخدام المفتاح الصحيح: user_id للمستخدم العادي، emp_id للموظف
            'employee_id' => $isEmployee ? $account->emp_id : $account->user_id,
            'name'        => $account->name,
            'role'        => $roleName, 
            'isLoggedIn'  => true,
            'isEmployee'  => $isEmployee, 
        ]);

        // التوجيه بناءً على الدور
        if ($roleName === 'warehouse') {
            return redirect()->to('/InventoryController/index');
        } elseif ($roleName === 'assets') {
            return redirect()->to('/AssetsController/dashboard');
        } elseif ($roleName === 'admin') {
            return redirect()->to('/AdminController/dashboard');
        } elseif ($roleName === 'user') {
            // التوجيه لصفحة العهد
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