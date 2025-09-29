<?php

namespace App\Controllers;

// Models
use App\Models\PermissionModel;
use App\Models\EmployeeModel;
use App\Models\RoleModel;

// Entities
use App\Entities\Employee;

// Exceptions
use App\Exceptions\AuthenticationException;
use App\Exceptions\ValidationException;
use App\Exceptions\AuthorizationException;

class AdminController extends BaseController
{
    public function dashboard(): string // Admin view
    {
        // Exception
        if (! session()->get('isLoggedIn')) {
            throw new AuthenticationException();
        }
        if (session()->get('role') !== 'admin') {
            throw new AuthorizationException();
        }
        return view('admin/admin_dashboard');
    }

    /**
     * @var PermissionModel
     */
    protected $permissionModel;

    /**
     * @var EmployeeModel
     */
    protected $employeeModel;

    /**
     * @var RoleModel
     */
    protected $roleModel;

    protected $helpers = ['form', 'url', 'session'];

    public function __construct()
    {
        $this->permissionModel = new PermissionModel();
        $this->employeeModel = new EmployeeModel();
        $this->roleModel = new RoleModel();
    }

    public function listPermissions(): string
    {
        $permissions = $this->permissionModel
            ->select('permission.*, employee.name as emp_name, role.name as role_name')
            ->join('employee', 'employee.emp_id = permission.emp_id')
            ->join('role', 'role.id = permission.role_id')
            ->findAll();

        $data = [
            'permissions' => $permissions,
            'message'     => session()->getFlashdata('message'),
            'status'      => session()->getFlashdata('status'),
        ];

        return view('admin/list_permissions', $data);
    }


    public function addPermission()
    {
        $data = [
            'employees' => $this->employeeModel->findAll(),
            'roles'     => $this->roleModel->findAll(),
        ];

        if ($this->request->is('post')) {

            try {
                $this->permissionModel->insertWithCheck($this->request->getPost());

                // نجاح الإضافة
                return redirect()->to(base_url('admin/listPermissions'))
                    ->with('status', 'success')
                    ->with('message', 'تم إضافة الصلاحية بنجاح');
            } catch (ValidationException $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $e->getErrors()); // جلب رسالةالخطأ
            }
        }

        return view('admin/add_permission_traditional', $data);
    }


    public function updatePermission($permissionId = null)
    {
        if (empty($permissionId)) {
            return redirect()->to(base_url('admin/addPermission'))
                ->with('status', 'error')
                ->with('message', 'معرف الصلاحية غير محدد لتحديثها.');
        }

        $permission = $this->permissionModel->find($permissionId);

        if (!$permission) {
            return redirect()->to(base_url('admin/addPermission'))
                ->with('status', 'error')
                ->with('message', 'الصلاحية المطلوبة غير موجودة.');
        }

        $data = [
            'permission' => $permission,
            'employees'  => $this->employeeModel->findAll(),
            'roles'      => $this->roleModel->findAll(),
        ];

        if ($this->request->is('post')) {
            try {
                // تمرير معرف الصلاحية للتحديث
                $postData = $this->request->getPost();
                $postData['id'] = $permissionId; // إضافة معرف الصلاحية للبيانات

                $this->permissionModel->updateWithCheck($permissionId, $postData);

                // نجاح التحديث
                return redirect()->to(base_url('admin/listPermissions'))
                    ->with('status', 'success')
                    ->with('message', 'تم تحديث الصلاحية بنجاح');
            } catch (ValidationException $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $e->getErrors()); // جلب رسالةالخطأ
            }
        }

        return view('admin/update_permission_traditional', $data);
    }


    /**
     * تحذف صلاحية معينة (ربط موظف بدور).
     *
     * @param int|null $permissionId معرف الصلاحية في جدول 'permission'.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function deletePermission($permissionId = null)
    {
        if (empty($permissionId)) {
            return redirect()->to(base_url('admin/addPermission')) // أو إلى صفحة قائمة الصلاحيات
                ->with('status', 'error')
                ->with('message', 'لم يتم تحديد معرف الصلاحية للحذف.');
        }

        $permission = $this->permissionModel->find($permissionId);

        if (!$permission) {
            return redirect()->to(base_url('admin/addPermission'))
                ->with('status', 'error')
                ->with('message', 'الصلاحية المطلوبة للحذف غير موجودة.');
        }

        // حفظ emp_id قبل حذف الصلاحية لإعادة التوجيه لاحقاً
        $empId = $permission->emp_id;

        if ($this->permissionModel->delete($permissionId)) {
            // إعادة التوجيه إلى صفحة معلومات الموظف بعد الحذف
            return redirect()->to(base_url('admin/listPermissions/' . $empId))
                ->with('status', 'success')
                ->with('message', 'تم حذف الصلاحية بنجاح');
        } else {
            return redirect()->to(base_url('admin/listPermissions/' . $empId)) // أو إلى نفس صفحة التحديث إذا كنت تريد
                ->with('status', 'success')
                ->with('message', 'فشل في حذف الصلاحية.');
        }
    }


    public function addEmployee()
    {
        if ($this->request->is('post')) {

            $postData = $this->request->getPost();

            // التحقق أولًا على البيانات الخام
            if (! $this->employeeModel->validate($postData)) {
                return redirect()->back()
                    ->with("errors", $this->employeeModel->errors())
                    ->withInput();
            }

            // إنشاء الانتتي بعد نجاح التحقق
            $employee = new Employee($postData);

            // تشفير كلمة المرور هنا فقط
            $employee->setPassword($postData['password']);

            // الحفظ
            $this->employeeModel->insert($employee);

            return redirect()->to(base_url('admin/addPermission'))
                ->with('status', 'success')
                ->with('message', 'تم إضافة الموظف بنجاح');
        }

        return view('admin/add_employee');
    }
}
