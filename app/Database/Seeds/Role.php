<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Role extends Seeder
{
    public function run()
    {
          //
          $data = [
            'id' => 2,
            'name' => 'admin',
           
        ];

        // Insert into 'users' table
        $this->db->table('role')->insert($data);
    
    
    }
}
