<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FinalTablesSeeder extends Seeder
{
    public function run()
    {
        $this->db->query('SET foreign_key_checks = 0;');

        // Seeder for 'role' table
        $roles = [
            ['name' => 'admin'],
            ['name' => 'warehouse'],
            ['name' => 'assets'],
            ['name' => 'user'],
            ['name' => 'super assets'],
            ['name' => 'super warehouse'],
        ];
        $this->db->table('role')->insertBatch($roles);

        // Seeder for 'order_status' table
        $order_statuses = [
            ['status' => 'قيد الانتظار'],
            ['status' => 'قيد التنفيذ'],
            ['status' => 'مكتمل'],
            ['status' => 'ملغي'],
        ];
        $this->db->table('order_status')->insertBatch($order_statuses);

        // Seeder for 'usage_status' table
        $usage_statuses = [
            ['usage_status' => 'جديد'],
            ['usage_status' => 'رجيع'],
        ];
        $this->db->table('usage_status')->insertBatch($usage_statuses);


        // Seeder for 'major_category' table
        $major_categories = [
            ['name' => 'Equipment - Non Medical'], // ID 1
            ['name' => 'Equipment - Medical'], // ID 2
            ['name' => 'Equipment_Medical_General'], // ID 3
            ['name' => 'Furniture - Medical'], // ID 4
            ['name' => 'Furniture - Non Medical'], // ID 5
            ['name' => 'Vehicles - Services'], // ID 6
            ['name' => 'Vehicles - Passengers'], // ID 7
        ];
        $this->db->table('major_category')->insertBatch($major_categories);

        // Seeder for 'minor_category' table
        $minor_categories = [
            ['name' => 'Ambulance', 'major_category_id' => 6], // Ambulances are 'Vehicles - Services' (ID 6) =>1
            ['name' => 'Bus', 'major_category_id' => 7], // Bus is a 'Vehicles - Passengers' (ID 7) =>2
            ['name' => 'Car', 'major_category_id' => 7], // Car is a 'Vehicles - Passengers' (ID 7) =>3
            ['name' => 'General', 'major_category_id' => 1], // Correctly linked to 'Equipment - Non Medical' (ID 1) =>4
            ['name' => 'Telecom', 'major_category_id' => 1], // Correctly linked to 'Equipment - Non Medical' (ID 1)=>5
            ['name' => 'IT', 'major_category_id' => 1], // Correctly linked to 'Equipment - Non Medical' (ID 1)=>6
            ['name' => 'Electric', 'major_category_id' => 1], // Correctly linked to 'Equipment - Non Medical' (ID 1)=>7
            ['name' => 'Kitchen', 'major_category_id' => 1], // Correctly linked to 'Equipment - Non Medical' (ID 1)=>8
            ['name' => 'Chairs', 'major_category_id' => 5], // Chairs are 'Furniture - Non Medical' (ID 5)=>9
            ['name' => 'Tables', 'major_category_id' => 5], // Tables are 'Furniture - Non Medical' (ID 5)=>10
            ['name' => 'Trollyes', 'major_category_id' => 4], // Correctly linked to 'Furniture - Medical' (ID 4)=>11
            ['name' => 'General-Medical', 'major_category_id' => 4], // Correctly linked to 'Equipment -  Medical' (ID 1) =>12

        ];
        $this->db->table('minor_category')->insertBatch($minor_categories);
        // Seeder for 'building' table
        $buildings = [
            ['code' => 'B1'],
            ['code' => 'B2'],
            ['code' => 'B3'],
        ];
        $this->db->table('building')->insertBatch($buildings);

        // Seeder for 'floor' table
        $floors = [
            ['code' => 'F1', 'building_id' => 1],
            ['code' => 'F2', 'building_id' => 1],
            ['code' => 'F3', 'building_id' => 2],
        ];
        $this->db->table('floor')->insertBatch($floors);

        // Seeder for 'section' table
        $sections = [
            ['code' => 'S1', 'floor_id' => 1],
            ['code' => 'S2', 'floor_id' => 1],
            ['code' => 'S3', 'floor_id' => 2],
        ];
        $this->db->table('section')->insertBatch($sections);

        // Seeder for 'room' table
        $rooms = [
            ['code' => 'R101', 'section_id' => 1],
            ['code' => 'R102', 'section_id' => 1],
            ['code' => 'R201', 'section_id' => 2],
        ];
        $this->db->table('room')->insertBatch($rooms);

        // Seeder for 'employee' table
        $employees = [
            [
                'emp_id'   => '1001',
                'name'     => 'فيّ النفيعي',
                'emp_dept' => 'المستودعات ',
                'emp_ext'  => 1234,
                'email'    => 'fay@example.com',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
            ],
            [
                'emp_id'   => '1002',
                'name'     => 'حميدة اختر',
                'emp_dept' => 'العهد',
                'emp_ext'  => 5678,
                'email'    => 'hmd@example.com',
                'password' => password_hash('123457', PASSWORD_DEFAULT),
            ],
            [
                'emp_id'   => '1003',
                'name'     => 'روان الرحيلي',
                'emp_dept' => 'المستودعات',
                'emp_ext'  => 9012,
                'email'    => 'raw123@example.com',
                'password' => password_hash('123458', PASSWORD_DEFAULT),
            ],
            [
                'emp_id'   => '1004',
                'name'     => 'سما سالم',
                'emp_dept' => 'العهد',
                'emp_ext'  => 1110,
                'email'    => 'sama@example.com',
                'password' => password_hash('123459', PASSWORD_DEFAULT),
            ],
        ];
        $this->db->table('employee')->insertBatch($employees);

        // Seeder for 'users' table
        $users = [
            [
                'user_id'   => 'U101',
                'name'      => 'علي محمد',
                'user_dept' => 'التسويق',
                'user_ext'  => 1111,
                'email'     => 'ali@example.com',
                'password'  => password_hash('123456', PASSWORD_DEFAULT),
            ],
            [
                'user_id'   => 'U102',
                'name'      => 'نورة فهد',
                'user_dept' => 'المبيعات',
                'user_ext'  => 2222,
                'email'     => 'noura@example.com',
                'password'  => password_hash('123458', PASSWORD_DEFAULT),
            ],
            [
                'user_id'   => 'U103',
                'name'      => 'بدر محمد',
                'user_dept' => 'الشؤون القانونية',
                'user_ext'  => 1119,
                'email'     => 'bader@example.com',
                'password'  => password_hash('123459', PASSWORD_DEFAULT),
            ],
            [
                'user_id'   => 'U104',
                'name'      => 'خالد فهد',
                'user_dept' => 'المالية',
                'user_ext'  => 2229,
                'email'     => 'khaled@example.com',
                'password'  => password_hash('1234560', PASSWORD_DEFAULT),
            ],
        ];
        $this->db->table('users')->insertBatch($users);

        // Seeder for 'items' table
        $items = [
            ['name' => '5915_ _PUMP, INFUSION, volumetric, STAND ALONE, 0.1 to 1000  ml/h_ _800802_ _ _22148017', 'minor_category_id' => 12],
            ['name' => 'كرسي طبي, متحرك دوار', 'minor_category_id' => 12],
            ['name' => 'كرسي طبي، متحرك', 'minor_category_id' => 12],
            ['name' => 'كرسي طبي، ثابت', 'minor_category_id' => 12],
            ['name' => 'تحويله مكتبيه، شبكيه، بشاشه صغيرة', 'minor_category_id' => 5],
            ['name' => 'شاشة', 'minor_category_id' => 6],
            ['name' => 'طابعة ملصقات', 'minor_category_id' => 6],
            ['name' => 'تحويله مكتبيه، شبكيه، بشاشه صغيره', 'minor_category_id' => 6],
            ['name' => 'جهاز حاسب آلي، ويندوز، طقم كامل (لوحة مفاتيح، فائرة، شاشة) ', 'minor_category_id' => 6],
            ['name' => 'ماسح ضوئي', 'minor_category_id' => 6],
            ['name' => 'فاكس, صغير', 'minor_category_id' => 5],
            ['name' => 'كاميرا مراقبه', 'minor_category_id' => 5],
            ['name' => 'هاتف محمول', 'minor_category_id' => 5],
            ['name' => 'راوتر', 'minor_category_id' => 5],
            ['name' => 'كرسي قماش ثابت', 'minor_category_id' => 9],
            ['name' => 'كرسي مكتبي متحرك جلد السوداء', 'minor_category_id' => 9],
            ['name' => '16-جمس-- اسعاف -2012-ابيض-ب و ا 7241-2 راكب ', 'minor_category_id' => 1],
            ['name' => 'عربة، معدنيه، 3 ارفف', 'minor_category_id' => 11],


        ];
        $this->db->table('items')->insertBatch($items);

        // Seeder for 'permission' table
        $permissions = [
            ['emp_id' => '1001', 'role_id' => 1, 'user_id' => null],
            ['emp_id' => '1002', 'role_id' => 2, 'user_id' => null],
            ['emp_id' => '1003', 'role_id' => 3, 'user_id' => null],
            ['emp_id' => '1004', 'role_id' => 6, 'user_id' => null],
            ['emp_id' => null, 'role_id' => 4, 'user_id' => 'U101'],
            ['emp_id' => null, 'role_id' => 4, 'user_id' => 'U102'],
            ['emp_id' => null, 'role_id' => 4, 'user_id' => 'U103'],
            ['emp_id' => null, 'role_id' => 4, 'user_id' => 'U104'],

        ];
        $this->db->table('permission')->insertBatch($permissions);

        // Seeder for 'order' table
        $orders = [
            [
                'from_user_id' => 'U101',
                'to_user_id' => 'U102',
                'order_status_id' => 1, // قيد الانتظار
                'note' => 'طلب مجموعة أجهزة حاسوب جديدة.',
            ],
            [
                'from_user_id' => 'U103',
                'to_user_id' => 'U104',
                'order_status_id' => 2, // قيد التنفيذ
                'note' => 'طلب صيانة دورية للطابعات.',
            ],
        ];
        $this->db->table('order')->insertBatch($orders);

        // Seeder for 'item_order' table
        $item_orders = [
            [
                'order_id' => 1,
                'item_id' => 6,
                'brand' => 'Dell',
                'quantity' => 1,
                'model_num' => 'FF1234',
                'asset_num' => '123456789101',
                'serial_num' => '123759789101',
                'old_asset_num' => '123456789102',
                'room_id' => 1,
                'assets_type' => 'عهدة خاصة',
                'created_by' => '1003',
                'usage_status_id' => 1, // جديد
                'note' => 'تم تسليم الشاشة لموظف الموارد البشرية.',
            ],
            [
                'order_id' => 2,
                'item_id' => 10,
                'brand' => 'Canon',
                'quantity' => 1,
                'model_num' => 'FC1111',
                'asset_num' => '123456789102',
                'serial_num' => '123456789001',
                'old_asset_num' => '123456789202',
                'room_id' => 3,
                'assets_type' => 'عهدة عامة',
                'created_by' => '1003',
                'usage_status_id' => 1, // جديد 
                'note' => 'راوتر جديد جاهز للاستخدام.',
            ],
        ];
        $this->db->table('item_order')->insertBatch($item_orders);

        // Seeder for 'attachments' table
        $attachments = [
            [
                'file_name' => 'مستند استلام.pdf',
                'file_path' => 'uploads/receipts/document_1.pdf'
            ],
        ];
        $this->db->table('attachments')->insertBatch($attachments);

        // Seeder for 'returned_items' table
        $returned_items = [
            [
                'item_order_id' => 1,
                'notes' => 'تم إرجاع الجهاز بحالة جيدة.',
                'attach_id' => 1,
            ],
        ];
        $this->db->table('returned_items')->insertBatch($returned_items);

        // Seeder for 'history' table
        $histories = [
            [
                'item_order_id' => 1,
                'action' => 'تم تسليم العهدة إلى الموظف.'
            ],
            [
                'item_order_id' => 2,
                'action' => 'تم طلب صيانة الجهاز.'
            ],
        ];
        $this->db->table('history')->insertBatch($histories);

        $this->db->query('SET foreign_key_checks = 1;');
    }
}
