<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Employee extends Seeder
{
    public function run()
    {
        //
          $data = [
            'id' => 2,
            'emp_id' => 33,
            'name' => 'nada',
            'password' => password_hash(123, PASSWORD_DEFAULT), // hashed password
            'email'    => 'nada@example.com',
      
        ];

        // Insert into 'users' table
        $this->db->table('employee')->insert($data);
    
    }
}
