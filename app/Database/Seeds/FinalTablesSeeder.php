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
            ['name' => 'super_assets'],
            ['name' => 'super_warehouse'],
            ['name' => 'IT_specialist'],
        ];
        $this->db->table('role')->insertBatch($roles);

        // Seeder for 'order_status' table
        $order_statuses = [
            ['status' => 'قيد الانتظار'],
            ['status' => 'مقبول'],
            ['status' => 'مرفوض'],
        ];
        $this->db->table('order_status')->insertBatch($order_statuses);

        // Seeder for 'usage_status' table
        $usage_statuses = [
            ['usage_status' => 'جديد'],
            ['usage_status' => 'رجيع'],
            ['usage_status' => 'تحويل'],
            ['usage_status' => 'معاد صرفة'],
            ['usage_status' => 'معادة للمرسل'],
            ['usage_status' => ' مستعمل'],


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
            ['name' => 'Ambulance', 'major_category_id' => 6], // ID 1
            ['name' => 'Bus', 'major_category_id' => 7], // ID 2
            ['name' => 'Car', 'major_category_id' => 7], // ID 3
            ['name' => 'General', 'major_category_id' => 1], // ID 4
            ['name' => 'Telecom', 'major_category_id' => 1], // ID 5
            ['name' => 'IT', 'major_category_id' => 1], // ID 6
            ['name' => 'Electric', 'major_category_id' => 1], // ID 7
            ['name' => 'Kitchen', 'major_category_id' => 1], // ID 8
            ['name' => 'Chairs', 'major_category_id' => 5], // ID 9
            ['name' => 'Tables', 'major_category_id' => 5], // ID 10
            ['name' => 'Trollyes', 'major_category_id' => 4], // ID 11
            ['name' => 'General', 'major_category_id' => 4], // ID 12
        ];
        $this->db->table('minor_category')->insertBatch($minor_categories);

        // Seeder for 'building' table
        $buildings = [
            ['code' => 'B1'], // ID 1
            ['code' => 'B2'], // ID 2
            ['code' => 'B3'], // ID 3
        ];
        $this->db->table('building')->insertBatch($buildings);

        // Seeder for 'floor' table
        $floors = [
            // أدوار المبنى B1
            ['code' => 'F1', 'building_id' => 1], // ID 1
            ['code' => 'F2', 'building_id' => 1], // ID 2
            ['code' => 'F3', 'building_id' => 1], // ID 3

            // أدوار المبنى B2
            ['code' => 'F1', 'building_id' => 2], // ID 4
            ['code' => 'F2', 'building_id' => 2], // ID 5
            ['code' => 'F3', 'building_id' => 2], // ID 6

            // أدوار المبنى B3
            ['code' => 'F1', 'building_id' => 3], // ID 7
            ['code' => 'F2', 'building_id' => 3], // ID 8
            ['code' => 'F3', 'building_id' => 3], // ID 9
        ];
        $this->db->table('floor')->insertBatch($floors);

        // Seeder for 'section' table
        $sections = [
            // أقسام B1-F1 (floor_id = 1)
            ['code' => 'S1', 'floor_id' => 1], // ID 1
            ['code' => 'S2', 'floor_id' => 1], // ID 2
            ['code' => 'S3', 'floor_id' => 1], // ID 3

            // أقسام B1-F2 (floor_id = 2)
            ['code' => 'S1', 'floor_id' => 2], // ID 4
            ['code' => 'S2', 'floor_id' => 2], // ID 5
            ['code' => 'S3', 'floor_id' => 2], // ID 6

            // أقسام B1-F3 (floor_id = 3)
            ['code' => 'S1', 'floor_id' => 3], // ID 7
            ['code' => 'S2', 'floor_id' => 3], // ID 8
            ['code' => 'S3', 'floor_id' => 3], // ID 9

            // أقسام B2-F1 (floor_id = 4)
            ['code' => 'S1', 'floor_id' => 4], // ID 10
            ['code' => 'S2', 'floor_id' => 4], // ID 11
            ['code' => 'S3', 'floor_id' => 4], // ID 12

            // أقسام B2-F2 (floor_id = 5)
            ['code' => 'S1', 'floor_id' => 5], // ID 13
            ['code' => 'S2', 'floor_id' => 5], // ID 14
            ['code' => 'S3', 'floor_id' => 5], // ID 15

            // أقسام B2-F3 (floor_id = 6)
            ['code' => 'S1', 'floor_id' => 6], // ID 16
            ['code' => 'S2', 'floor_id' => 6], // ID 17
            ['code' => 'S3', 'floor_id' => 6], // ID 18

            // أقسام B3-F1 (floor_id = 7)
            ['code' => 'S1', 'floor_id' => 7], // ID 19
            ['code' => 'S2', 'floor_id' => 7], // ID 20
            ['code' => 'S3', 'floor_id' => 7], // ID 21

            // أقسام B3-F2 (floor_id = 8)
            ['code' => 'S1', 'floor_id' => 8], // ID 22
            ['code' => 'S2', 'floor_id' => 8], // ID 23
            ['code' => 'S3', 'floor_id' => 8], // ID 24

            // أقسام B3-F3 (floor_id = 9)
            ['code' => 'S1', 'floor_id' => 9], // ID 25
            ['code' => 'S2', 'floor_id' => 9], // ID 26
            ['code' => 'S3', 'floor_id' => 9], // ID 27
        ];
        $this->db->table('section')->insertBatch($sections);

        // Seeder for 'room' table
        $rooms = [
            // غرف B1-F1-S1 (section_id = 1)
            ['code' => 'R101', 'section_id' => 1],
            ['code' => 'R102', 'section_id' => 1],
            ['code' => 'R103', 'section_id' => 1],

            // غرف B1-F1-S2 (section_id = 2)
            ['code' => 'R201', 'section_id' => 2],
            ['code' => 'R202', 'section_id' => 2],
            ['code' => 'R203', 'section_id' => 2],

            // غرف B1-F1-S3 (section_id = 3)
            ['code' => 'R301', 'section_id' => 3],
            ['code' => 'R302', 'section_id' => 3],
            ['code' => 'R303', 'section_id' => 3],

            // غرف B1-F2-S1 (section_id = 4)
            ['code' => 'R101', 'section_id' => 4],
            ['code' => 'R102', 'section_id' => 4],
            ['code' => 'R103', 'section_id' => 4],

            // غرف B1-F2-S2 (section_id = 5)
            ['code' => 'R201', 'section_id' => 5],
            ['code' => 'R202', 'section_id' => 5],
            ['code' => 'R203', 'section_id' => 5],

            // غرف B1-F2-S3 (section_id = 6)
            ['code' => 'R301', 'section_id' => 6],
            ['code' => 'R302', 'section_id' => 6],
            ['code' => 'R303', 'section_id' => 6],

            // غرف B1-F3-S1 (section_id = 7)
            ['code' => 'R101', 'section_id' => 7],
            ['code' => 'R102', 'section_id' => 7],
            ['code' => 'R103', 'section_id' => 7],

            // غرف B1-F3-S2 (section_id = 8)
            ['code' => 'R201', 'section_id' => 8],
            ['code' => 'R202', 'section_id' => 8],
            ['code' => 'R203', 'section_id' => 8],

            // غرف B1-F3-S3 (section_id = 9)
            ['code' => 'R301', 'section_id' => 9],
            ['code' => 'R302', 'section_id' => 9],
            ['code' => 'R303', 'section_id' => 9],

            // غرف B2-F1-S1 (section_id = 10)
            ['code' => 'R101', 'section_id' => 10],
            ['code' => 'R102', 'section_id' => 10],
            ['code' => 'R103', 'section_id' => 10],

            // غرف B2-F1-S2 (section_id = 11)
            ['code' => 'R201', 'section_id' => 11],
            ['code' => 'R202', 'section_id' => 11],
            ['code' => 'R203', 'section_id' => 11],

            // غرف B2-F1-S3 (section_id = 12)
            ['code' => 'R301', 'section_id' => 12],
            ['code' => 'R302', 'section_id' => 12],
            ['code' => 'R303', 'section_id' => 12],

            // غرف B2-F2-S1 (section_id = 13)
            ['code' => 'R101', 'section_id' => 13],
            ['code' => 'R102', 'section_id' => 13],
            ['code' => 'R103', 'section_id' => 13],

            // غرف B2-F2-S2 (section_id = 14)
            ['code' => 'R201', 'section_id' => 14],
            ['code' => 'R202', 'section_id' => 14],
            ['code' => 'R203', 'section_id' => 14],

            // غرف B2-F2-S3 (section_id = 15)
            ['code' => 'R301', 'section_id' => 15],
            ['code' => 'R302', 'section_id' => 15],
            ['code' => 'R303', 'section_id' => 15],

            // غرف B2-F3-S1 (section_id = 16)
            ['code' => 'R101', 'section_id' => 16],
            ['code' => 'R102', 'section_id' => 16],
            ['code' => 'R103', 'section_id' => 16],

            // غرف B2-F3-S2 (section_id = 17)
            ['code' => 'R201', 'section_id' => 17],
            ['code' => 'R202', 'section_id' => 17],
            ['code' => 'R203', 'section_id' => 17],

            // غرف B2-F3-S3 (section_id = 18)
            ['code' => 'R301', 'section_id' => 18],
            ['code' => 'R302', 'section_id' => 18],
            ['code' => 'R303', 'section_id' => 18],

            // غرف B3-F1-S1 (section_id = 19)
            ['code' => 'R101', 'section_id' => 19],
            ['code' => 'R102', 'section_id' => 19],
            ['code' => 'R103', 'section_id' => 19],

            // غرف B3-F1-S2 (section_id = 20)
            ['code' => 'R201', 'section_id' => 20],
            ['code' => 'R202', 'section_id' => 20],
            ['code' => 'R203', 'section_id' => 20],

            // غرف B3-F1-S3 (section_id = 21)
            ['code' => 'R301', 'section_id' => 21],
            ['code' => 'R302', 'section_id' => 21],
            ['code' => 'R303', 'section_id' => 21],

            // غرف B3-F2-S1 (section_id = 22)
            ['code' => 'R101', 'section_id' => 22],
            ['code' => 'R102', 'section_id' => 22],
            ['code' => 'R103', 'section_id' => 22],

            // غرف B3-F2-S2 (section_id = 23)
            ['code' => 'R201', 'section_id' => 23],
            ['code' => 'R202', 'section_id' => 23],
            ['code' => 'R203', 'section_id' => 23],

            // غرف B3-F2-S3 (section_id = 24)
            ['code' => 'R301', 'section_id' => 24],
            ['code' => 'R302', 'section_id' => 24],
            ['code' => 'R303', 'section_id' => 24],

            // غرف B3-F3-S1 (section_id = 25)
            ['code' => 'R101', 'section_id' => 25],
            ['code' => 'R102', 'section_id' => 25],
            ['code' => 'R103', 'section_id' => 25],

            // غرف B3-F3-S2 (section_id = 26)
            ['code' => 'R201', 'section_id' => 26],
            ['code' => 'R202', 'section_id' => 26],
            ['code' => 'R203', 'section_id' => 26],

            // غرف B3-F3-S3 (section_id = 27)
            ['code' => 'R301', 'section_id' => 27],
            ['code' => 'R302', 'section_id' => 27],
            ['code' => 'R303', 'section_id' => 27],
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
            [
                'emp_id'   => '1005',
                'name'     => 'ليان السيد',
                'emp_dept' => 'المستودعات',
                'emp_ext'  => 5000,
                'email'    => 'layan@example.com',
                'password' => password_hash('123455', PASSWORD_DEFAULT),
            ],
            [
                'emp_id'   => '1006',
                'name'     => ' ليالي العلياني',
                'emp_dept' => 'المستودعات',
                'emp_ext'  => 5000,
                'email'    => 'layalialalyani@gmail.com',
                'password' => password_hash('123445', PASSWORD_DEFAULT),
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
            [
                'user_id'   => 'U105',
                'name'      => ' مهندسة ليان',
                'user_dept' => 'المالية',
                'user_ext'  => 5559,
                'email'     => 'laymix2005@gmail.com',
                'password'  => password_hash('123455', PASSWORD_DEFAULT),
            ],
            [
                'user_id'   => 'U106',
                'name'      => ' مهندسة سما',
                'user_dept' => 'المالية',
                'user_ext'  => 9999,
                'email'     => 'sama.alwafi3@gmail.com',
                'password'  => password_hash('123455', PASSWORD_DEFAULT),
            ],
            [
                'user_id'   => '1002',
                'name'     => 'حميدة اختر',
                'user_dept' => 'العهد',
                'user_ext'  => 5678,
                'email'    => 'hmd@example.com',
                'password' => password_hash('123457', PASSWORD_DEFAULT),
            ],
            [
                'user_id'   => '1006',
                'name'     => ' ليالي العلياني',
                'user_dept' => 'المستودعات',
                'user_ext'  => 5000,
                'email'    => 'layalialalyani@gmail.com',
                'password' => password_hash('123445', PASSWORD_DEFAULT),
            ],
        ];
        $this->db->table('users')->insertBatch($users);

        // Seeder for 'items' table
        $items = [
            ['name' => '5915_ _PUMP, INFUSION, volumetric, STAND ALONE, 0.1 to 1000  ml/h_ _800802_ _ _22148017', 'minor_category_id' => 12],
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
            ['emp_id' => '1001', 'role_id' => 1],
            ['emp_id' => '1002', 'role_id' => 2],
            ['emp_id' => '1003', 'role_id' => 3],
            ['emp_id' => '1004', 'role_id' => 5],
            ['emp_id' => '1005', 'role_id' => 7],
            ['emp_id' => '1006', 'role_id' => 6],
        ];
        $this->db->table('permission')->insertBatch($permissions);

        // Seeder for 'order' table
        $orders = [
            [
                'from_user_id' => 'U101',
                'to_user_id' => 'U102',
                'order_status_id' => 1,
                'note' => 'طلب مجموعة أجهزة حاسوب جديدة.',
            ],
            [
                'from_user_id' => 'U103',
                'to_user_id' => 'U104',
                'order_status_id' => 2,
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
                'usage_status_id' => 1,
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
                'usage_status_id' => 1,
                'note' => 'راوتر جديد جاهز للاستخدام.',
            ],
        ];
        $this->db->table('item_order')->insertBatch($item_orders);



        // Seeder for 'returned_items' table
        $returned_items = [
            [
                'item_order_id' => 1,
                'notes' => 'تم إرجاع الجهاز بحالة جيدة.',
            ],
        ];
        $this->db->table('returned_items')->insertBatch($returned_items);

        // Seeder for 'transfer_items' table
        $transfer_items = [
            [
                'item_order_id' => 1,
                'from_user_id' => 'U101',
                'to_user_id' => 'U102',
                'order_status_id' => 2,
                'note' => 'تحويل الشاشة من قسم التسويق إلى قسم المبيعات',
            ],
            [
                'item_order_id' => 2,
                'from_user_id' => 'U103',
                'to_user_id' => 'U104',
                'order_status_id' => 1,
                'note' => 'تحويل الراوتر من الشؤون القانونية إلى المالية',
            ],
            [
                'item_order_id' => 1,
                'from_user_id' => 'U102',
                'to_user_id' => 'U103',
                'order_status_id' => 2,
                'note' => 'تحويل مؤقت للشاشة لحين الانتهاء من المشروع',
            ],
        ];

        $this->db->table('transfer_items')->insertBatch($transfer_items);



        // $this->db->query('SET foreign_key_checks = 1;');
    }
}
