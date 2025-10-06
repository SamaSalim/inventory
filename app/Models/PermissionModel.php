<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Permission;
use App\Exceptions\ValidationException; //Exception


class PermissionModel extends Model
{
    protected $table         = 'permission';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;
    protected $returnType    = Permission::class;
    protected $allowedFields = ['emp_id','user_id','role_id'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'emp_id'  => 'required|is_not_unique[employee.emp_id]',
        'user_id' => 'required|is_not_unique[users.user_id]',
        'role_id' => 'required|numeric|is_not_unique[role.id]',
    ];

    protected $validationMessages = [
        'emp_id' => [
            'required'      => 'يجب تحديد الموظف.',
            'is_not_unique' => 'الموظف المحدد غير موجود.',
        ],
        'user_id' => [
            'required'      => 'يجب تحديد الموظف.',
            'is_not_unique' => 'الموظف المحدد غير موجود.',
        ],
        'role_id' => [
            'required'      => 'يجب تحديد الدور.',
            'numeric'       => 'معرف الدور يجب أن يكون رقمًا.',
            'is_not_unique' => 'الدور المحدد غير موجود.',
        ],
    ];

    public function insertWithCheck(array $data)
    {
        // تحقق من التكرار
        $exists = $this
            ->where('emp_id', $data['emp_id'])
            ->where('role_id', $data['role_id'])
            ->first();

        if ($exists) {
            throw new ValidationException(
                ['permission' => 'هذه الصلاحية موجودة بالفعل لهذا الموظف.'],
                'خطأ في التحقق'
            );
        }

        // تحقق من القواعد باستخدام built-in validation
        if (! $this->validate($data)) {
            throw new ValidationException(
                $this->errors(), // مصفوفة الأخطاء من CodeIgniter
                'خطأ في التحقق من البيانات'
            );
        }

        // إذا كل شيء صحيح، أضف البيانات
        return $this->insert($data);
    }


    public function updateWithCheck($permissionId, $data)
    {
        // نضيف id للبيانات (مطلوبة للتحقق من التكرار في حالة update)
        $data['id'] = $permissionId;

        // استخدام الـ validation rules المدمجة
        if (! $this->validate($data)) {
            throw new ValidationException(
                $this->errors(),
                'خطأ في التحقق من البيانات'
            );
        }


        // تحقق من التكرار
        $exists = $this
            ->where('emp_id', $data['emp_id'])
            ->where('role_id', $data['role_id'])
            ->first();

        if ($exists) {
            throw new ValidationException(
                ['permission' => 'الصلاحية والدور موجودة بالفعل'],
                'خطأ في التحقق'
            );
        }


        // تحديث البيانات
        return $this->update($permissionId, [
            'emp_id'  => $data['emp_id'],
            'role_id' => $data['role_id']
        ]);
    }
}
