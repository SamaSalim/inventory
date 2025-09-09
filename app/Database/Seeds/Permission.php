<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Permission extends Seeder
{
    public function run()
    {
         //
          $data = [
            'id' => 2,
            'role_id' => 2,
            'emp_id' => 33,
        ];

        // Insert into 'users' table
        $this->db->table('permission')->insert($data);
    
    
    }
}


