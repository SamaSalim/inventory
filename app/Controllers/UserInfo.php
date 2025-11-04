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

        $isEmployee = session()->get('isEmployee');
        $account_id = $isEmployee ? session()->get('employee_id') : session()->get('user_id');
        $currentRole = session()->get('role'); // جلب الدور من الجلسة (للمستخدم العادي هو 'user')

        $account = null;
        if ($isEmployee) {
            $employeeModel = new EmployeeModel();
            $account = $employeeModel->where('emp_id', $account_id)->first();
        } else {
            $userModel = new UserModel();
            // البحث باستخدام user_id المخزن في الجلسة
            $account = $userModel->where('user_id', $account_id)->first();
        }

        if (!$account) {
            $data['error_message'] = 'المستخدم غير موجود في النظام';
            return view('user/user_info', $data);
        }

        $permissionModel = new PermissionModel();

        // جلب الأدوار بناءً على نوع الحساب
        $permissions = null;
        $roles = [];

        if ($isEmployee) {
            // للموظف: لا يزال يتم جلب الصلاحيات من جدول permission بناءً على emp_id
            $permissions = $permissionModel->select('role.*')
                ->join('role', 'role.id = permission.role_id')
                ->where('permission.emp_id', $account_id)
                ->findAll();

            // تحويل نتائج قاعدة البيانات إلى مصفوفة الأدوار
            foreach ($permissions as $permission) {
                $roles[] = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ];
            }
        } else {
            //  للمستخدم العادي، يتم إلغاء محاولة البحث في جدول permission 
            // ويعتمد الدور على ما تم تخزينه في الجلسة ('user') بشكل افتراضي.
            $roles[] = [
                'id' => 0, // ID وهمي
                'name' => $currentRole,
            ];
        }

        $account->roles = $roles;
        $data['account'] = $account;
        $data['user_roles_array'] = array_column($roles, 'name');
        $data['user_role'] = $currentRole; // استخدام الدور الذي تم جلبه من الجلسة

        return view('user/user_info', $data);
    }
}
