<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\PermissionModel;
use App\Models\RoleModel;
use CodeIgniter\Controller;


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
        $employee = $employeeModel->where('emp_id', $empID)->first();

        if (!$employee || !password_verify($password, $employee->password)) {
            return redirect()->back()->with('error', 'Invalid credentials');
        }

        $permissionModel = new PermissionModel();
        $roleModel = new RoleModel();

        $permission = $permissionModel->where('emp_id', $employee->emp_id)->first();

        if (!$permission || empty($permission->role_id)) {
            return redirect()->back()->with('error', 'No role assigned or invalid permission data');
        }

        $role = $roleModel->find($permission->role_id);

        if (!$role) {
            return redirect()->back()->with('error', 'Invalid role');
        }

        session()->set([
            'employee_id' => $employee->emp_id, 
            'name'        => $employee->name,
            'role'        => $role->name,
            'isLoggedIn'  => true
        ]);

        if ($role->name === 'warehouse') {
            return redirect()->to('/warehouse/dashboard');

        } elseif ($role->name === 'assets') {
            return redirect()->to('/assets/dashboard');
            
        }
        elseif ($role->name === 'admin') {
            return redirect()->to('/admin/dashboard');
            
        } else {
            return redirect()->back()->with('error', 'Unknown role');
        }
        
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}