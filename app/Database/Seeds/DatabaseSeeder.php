<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // تعطيل فحص المفاتيح الخارجية مؤقتاً
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        // 1. جدول role - مع التحقق من وجود البيانات
        if ($this->db->table('role')->countAllResults() == 0) {
            $this->db->table('role')->insertBatch([
                ['name' => 'مدير النظام'],
                ['name' => 'موظف المستودع'],
                ['name' => 'مشرف القسم'],
                ['name' => 'موظف عادي'],
            ]);
        }

        // 2. جدول order_status
        if ($this->db->table('order_status')->countAllResults() == 0) {
            $this->db->table('order_status')->insertBatch([
                ['status' => 'جديد'],
                ['status' => 'قيد المعالجة'],
                ['status' => 'مكتمل'],
                ['status' => 'ملغي'],
                ['status' => 'مؤجل'],
            ]);
        }

        // 3. جدول usage_status
        if ($this->db->table('usage_status')->countAllResults() == 0) {
            $this->db->table('usage_status')->insertBatch([
                ['usage_status' => 'متاح'],
                ['usage_status' => 'قيد الاستخدام'],
                ['usage_status' => 'خارج الخدمة'],
                ['usage_status' => 'تحت الصيانة'],
                ['usage_status' => 'مفقود'],
                ['usage_status' => 'تالف'],
            ]);
        }

        // 4. جدول major_category
        if ($this->db->table('major_category')->countAllResults() == 0) {
            $this->db->table('major_category')->insertBatch([
                ['name' => 'أجهزة الكمبيوتر'],
                ['name' => 'الأثاث المكتبي'],
                ['name' => 'أجهزة الشبكة'],
                ['name' => 'المعدات الطبية'],
                ['name' => 'أدوات القرطاسية'],
                ['name' => 'الأجهزة الكهربائية'],
            ]);
        }

        // 5. جدول minor_category
        if ($this->db->table('minor_category')->countAllResults() == 0) {
            $this->db->table('minor_category')->insertBatch([
                // فئات فرعية لأجهزة الكمبيوتر
                ['name' => 'أجهزة كمبيوتر مكتبية', 'major_category_id' => 1],
                ['name' => 'أجهزة لابتوب', 'major_category_id' => 1],
                ['name' => 'شاشات', 'major_category_id' => 1],
                ['name' => 'طابعات', 'major_category_id' => 1],
                
                // فئات فرعية للأثاث المكتبي
                ['name' => 'مكاتب', 'major_category_id' => 2],
                ['name' => 'كراسي', 'major_category_id' => 2],
                ['name' => 'خزائن', 'major_category_id' => 2],
                ['name' => 'طاولات اجتماعات', 'major_category_id' => 2],
                
                // فئات فرعية لأجهزة الشبكة
                ['name' => 'راوترات', 'major_category_id' => 3],
                ['name' => 'سويتشات', 'major_category_id' => 3],
                ['name' => 'كابلات شبكة', 'major_category_id' => 3],
                
                // فئات فرعية للمعدات الطبية
                ['name' => 'أجهزة قياس ضغط', 'major_category_id' => 4],
                ['name' => 'ميزان حرارة', 'major_category_id' => 4],
                
                // فئات فرعية للقرطاسية
                ['name' => 'أوراق', 'major_category_id' => 5],
                ['name' => 'أقلام', 'major_category_id' => 5],
                
                // فئات فرعية للأجهزة الكهربائية
                ['name' => 'مكيفات', 'major_category_id' => 6],
                ['name' => 'مراوح', 'major_category_id' => 6],
            ]);
        }

        // 6. جدول building
        if ($this->db->table('building')->countAllResults() == 0) {
            $this->db->table('building')->insertBatch([
                ['code' => 'A'],
                ['code' => 'B'],
                ['code' => 'C'],
                ['code' => 'D'],
            ]);
        }

        // 7. جدول floor
        if ($this->db->table('floor')->countAllResults() == 0) {
            $this->db->table('floor')->insertBatch([
                ['code' => 'A-F1', 'building_id' => 1],
                ['code' => 'A-F2', 'building_id' => 1],
                ['code' => 'A-F3', 'building_id' => 1],
                ['code' => 'B-F1', 'building_id' => 2],
                ['code' => 'B-F2', 'building_id' => 2],
                ['code' => 'C-F1', 'building_id' => 3],
                ['code' => 'D-F1', 'building_id' => 4],
            ]);
        }

        // 8. جدول section
        if ($this->db->table('section')->countAllResults() == 0) {
            $this->db->table('section')->insertBatch([
                ['code' => 'A-F1-S1', 'floor_id' => 1],
                ['code' => 'A-F1-S2', 'floor_id' => 1],
                ['code' => 'A-F1-S3', 'floor_id' => 1],
                ['code' => 'A-F2-S1', 'floor_id' => 2],
                ['code' => 'A-F2-S2', 'floor_id' => 2],
                ['code' => 'B-F1-S1', 'floor_id' => 4],
                ['code' => 'B-F1-S2', 'floor_id' => 4],
                ['code' => 'C-F1-S1', 'floor_id' => 6],
                ['code' => 'D-F1-S1', 'floor_id' => 7],
            ]);
        }

        // 9. جدول room
        if ($this->db->table('room')->countAllResults() == 0) {
            $this->db->table('room')->insertBatch([
                ['code' => 'A-F1-S1-R101', 'section_id' => 1],
                ['code' => 'A-F1-S1-R102', 'section_id' => 1],
                ['code' => 'A-F1-S1-R103', 'section_id' => 1],
                ['code' => 'A-F1-S2-R201', 'section_id' => 2],
                ['code' => 'A-F1-S2-R202', 'section_id' => 2],
                ['code' => 'A-F2-S1-R301', 'section_id' => 4],
                ['code' => 'A-F2-S1-R302', 'section_id' => 4],
                ['code' => 'B-F1-S1-R401', 'section_id' => 6],
                ['code' => 'B-F1-S1-R402', 'section_id' => 6],
                ['code' => 'B-F1-S2-R501', 'section_id' => 7],
                ['code' => 'C-F1-S1-R601', 'section_id' => 8],
                ['code' => 'D-F1-S1-R701', 'section_id' => 9],
            ]);
        }

        // 10. جدول employee
        if ($this->db->table('employee')->countAllResults() == 0) {
            $this->db->table('employee')->insertBatch([
                [
                    'emp_id' => 'EMP001',
                    'name' => 'أحمد محمد الأحمد',
                    'emp_dept' => 'تقنية المعلومات',
                    'emp_ext' => 1001,
                    'email' => 'ahmed@company.com',
                    'password' => password_hash('123456', PASSWORD_DEFAULT),
                ],
                [
                    'emp_id' => 'EMP002',
                    'name' => 'فاطمة علي السعد',
                    'emp_dept' => 'المستودعات',
                    'emp_ext' => 1002,
                    'email' => 'fatima@company.com',
                    'password' => password_hash('123456', PASSWORD_DEFAULT),
                ],
                [
                    'emp_id' => 'EMP003',
                    'name' => 'محمد سعد الغامدي',
                    'emp_dept' => 'الموارد البشرية',
                    'emp_ext' => 1003,
                    'email' => 'mohammed@company.com',
                    'password' => password_hash('123456', PASSWORD_DEFAULT),
                ],
                [
                    'emp_id' => 'EMP004',
                    'name' => 'نورا خالد المطيري',
                    'emp_dept' => 'المالية',
                    'emp_ext' => 1004,
                    'email' => 'nora@company.com',
                    'password' => password_hash('123456', PASSWORD_DEFAULT),
                ],
                [
                    'emp_id' => 'EMP005',
                    'name' => 'عبدالله أحمد الزهراني',
                    'emp_dept' => 'الشؤون الطبية',
                    'emp_ext' => 1005,
                    'email' => 'abdullah@company.com',
                    'password' => password_hash('123456', PASSWORD_DEFAULT),
                ],
            ]);
        }

        // 11. جدول items
        if ($this->db->table('items')->countAllResults() == 0) {
            $this->db->table('items')->insertBatch([
                ['name' => 'جهاز كمبيوتر Dell OptiPlex', 'minor_category_id' => 1],
                ['name' => 'لابتوب HP ProBook', 'minor_category_id' => 2],
                ['name' => 'شاشة Samsung 24 بوصة', 'minor_category_id' => 3],
                ['name' => 'طابعة HP LaserJet', 'minor_category_id' => 4],
                ['name' => 'مكتب خشبي', 'minor_category_id' => 5],
                ['name' => 'كرسي مكتبي', 'minor_category_id' => 6],
                ['name' => 'خزانة معدنية', 'minor_category_id' => 7],
                ['name' => 'راوتر Cisco', 'minor_category_id' => 9],
                ['name' => 'سويتش 24 منفذ', 'minor_category_id' => 10],
                ['name' => 'جهاز قياس ضغط رقمي', 'minor_category_id' => 12],
                ['name' => 'ميزان حرارة طبي', 'minor_category_id' => 13],
                ['name' => 'مكيف هواء سبليت', 'minor_category_id' => 16],
            ]);
        }

        // 12. جدول order
        if ($this->db->table('order')->countAllResults() == 0) {
            $this->db->table('order')->insertBatch([
                [
                    'from_employee_id' => 'EMP003',
                    'to_employee_id' => 'EMP002',
                    'order_status_id' => 2,
                    'note' => 'طلب أجهزة كمبيوتر للقسم الجديد',
                ],
                [
                    'from_employee_id' => 'EMP004',
                    'to_employee_id' => 'EMP002',
                    'order_status_id' => 3,
                    'note' => 'طلب أثاث مكتبي',
                ],
                [
                    'from_employee_id' => 'EMP005',
                    'to_employee_id' => 'EMP002',
                    'order_status_id' => 1,
                    'note' => 'طلب معدات طبية',
                ],
            ]);
        }

        // 13. جدول item_order - مع الأعمدة الصحيحة حسب هيكل الجدول
        if ($this->db->table('item_order')->countAllResults() == 0) {
            $this->db->table('item_order')->insertBatch([
                [
                    'order_id' => 1,
                    'item_id' => 1,
                    'brand' => 'Dell',
                    'quantity' => 5,
                    'model_num' => 'OptiPlex 3080',
                    'asset_num' => 'AST001001',
                    'serial_num' => 'DL001001',
                    'old_asset_num' => null, // العمود المفقود
                    'room_id' => 1,
                    'assets_type' => 'عهدة عامة',
                    'created_by' => 'EMP002',
                    'usage_status_id' => 2,
                    'note' => 'أجهزة جديدة للقسم',
                ],
                [
                    'order_id' => 1,
                    'item_id' => 3,
                    'brand' => 'Samsung',
                    'quantity' => 5,
                    'model_num' => 'S24F350FH',
                    'asset_num' => 'AST002001',
                    'serial_num' => 'SM001001',
                    'old_asset_num' => null,
                    'room_id' => 1,
                    'assets_type' => 'عهدة عامة',
                    'created_by' => 'EMP002',
                    'usage_status_id' => 2,
                    'note' => null,
                ],
                [
                    'order_id' => 2,
                    'item_id' => 5,
                    'brand' => 'IKEA',
                    'quantity' => 3,
                    'model_num' => 'BEKANT',
                    'asset_num' => 'AST003001',
                    'serial_num' => 'IK001001',
                    'old_asset_num' => null,
                    'room_id' => 2,
                    'assets_type' => 'عهدة عامة',
                    'created_by' => 'EMP002',
                    'usage_status_id' => 2,
                    'note' => null,
                ],
                [
                    'order_id' => 2,
                    'item_id' => 6,
                    'brand' => 'Herman Miller',
                    'quantity' => 3,
                    'model_num' => 'Aeron',
                    'asset_num' => 'AST004001',
                    'serial_num' => 'HM001001',
                    'old_asset_num' => null,
                    'room_id' => 2,
                    'assets_type' => 'عهدة عامة',
                    'created_by' => 'EMP002',
                    'usage_status_id' => 2,
                    'note' => null,
                ],
                [
                    'order_id' => 3,
                    'item_id' => 10,
                    'brand' => 'Omron',
                    'quantity' => 2,
                    'model_num' => 'HEM-7120',
                    'asset_num' => 'AST005001',
                    'serial_num' => 'OM001001',
                    'old_asset_num' => null,
                    'room_id' => 8,
                    'assets_type' => 'عهدة خاصة',
                    'created_by' => 'EMP002',
                    'usage_status_id' => 1,
                    'note' => 'للعيادة الطبية',
                ],
                [
                    'order_id' => null,
                    'item_id' => 2,
                    'brand' => 'HP',
                    'quantity' => 2,
                    'model_num' => 'ProBook 450 G8',
                    'asset_num' => 'AST006001',
                    'serial_num' => 'HP001001',
                    'old_asset_num' => null,
                    'room_id' => 3,
                    'assets_type' => 'عهدة خاصة',
                    'created_by' => 'EMP002',
                    'usage_status_id' => 2,
                    'note' => 'للموظفين المتنقلين',
                ],
            ]);
        }

        // 14. جدول attachments
        if ($this->db->table('attachments')->countAllResults() == 0) {
            $this->db->table('attachments')->insertBatch([
                [
                    'file_name' => 'invoice_001.pdf',
                    'file_path' => '/uploads/invoices/invoice_001.pdf',
                ],
                [
                    'file_name' => 'warranty_dell_001.pdf',
                    'file_path' => '/uploads/warranties/warranty_dell_001.pdf',
                ],
                [
                    'file_name' => 'damage_report_001.jpg',
                    'file_path' => '/uploads/reports/damage_report_001.jpg',
                ],
            ]);
        }

        // 15. جدول returned_items
        if ($this->db->table('returned_items')->countAllResults() == 0) {
            $this->db->table('returned_items')->insertBatch([
                [
                    'item_order_id' => 1,
                    'notes' => 'جهاز تالف - يحتاج صيانة',
                    'attach_id' => 3,
                ],
            ]);
        }

        // 16. جدول history
        if ($this->db->table('history')->countAllResults() == 0) {
            $this->db->table('history')->insertBatch([
                ['item_order_id' => 1],
                ['item_order_id' => 2],
                ['item_order_id' => 3],
                ['item_order_id' => 4],
                ['item_order_id' => 5],
                ['item_order_id' => 6],
            ]);
        }

        // 17. جدول permission
        if ($this->db->table('permission')->countAllResults() == 0) {
            $this->db->table('permission')->insertBatch([
                ['emp_id' => 'EMP001', 'role_id' => 1],
                ['emp_id' => 'EMP002', 'role_id' => 2],
                ['emp_id' => 'EMP003', 'role_id' => 3],
                ['emp_id' => 'EMP004', 'role_id' => 4],
                ['emp_id' => 'EMP005', 'role_id' => 4],
            ]);
        }

        // إعادة تفعيل فحص المفاتيح الخارجية
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        echo "تم إدراج البيانات التجريبية بنجاح!" . PHP_EOL;
    }
}