<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitSeeder extends Seeder
{
    public function run()
    {
        // 1. إدخال الموظف
        $employeeData = [
            'id'       => 2,
            'emp_id'   => 33,
            'name'     => 'nada',
            'password' => password_hash('123', PASSWORD_DEFAULT), // لاحظ خليها نص '123'
            'email'    => 'nada@example.com',
        ];
        $this->db->table('employee')->insert($employeeData);

        // 2. إدخال الدور
        $roleData = [
            'id'   => 2,
            'name' => 'admin',
        ];
        $this->db->table('role')->insert($roleData);

        // 3. إدخال الصلاحية
        $permissionData = [
            'id'      => 2,
            'role_id' => 2,
            'emp_id'  => 33,
        ];
        $this->db->table('permission')->insert($permissionData);
    }
}
