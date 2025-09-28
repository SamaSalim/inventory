<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\UsageStatus;

class UsageStatusModel extends Model
{
    protected $table            = 'usage_status'; // اسم الجدول
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['usage_status'];
    protected $returnType       = UsageStatus::class;

    protected $useTimestamps    = true; 

   protected $validationRules = [
        'usage_status' => 'permit_empty|max_length[50]|is_unique[usage_status.usage_status,id,{id}]'
    ];
    protected $validationMessages = [
        'usage_status' => [
            'max_length' => 'حالة الاستخدام لا يمكن أن تزيد عن 50 حرف',
            'is_unique' => 'حالة الاستخدام موجودة مسبقاً'
        ]
    ];
}
