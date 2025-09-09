<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Section;

class SectionModel extends Model
{
    protected $table = 'section';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Section::class;
    protected $allowedFields = ['code', 'floor_id'];
    protected $useTimestamps = false; // No timestamps in migration
    protected $validationRules = [
        'code' => 'required|max_length[50]|is_unique[section.code,id,{id}]',
        'floor_id' => 'required|integer|is_not_unique[floor.id]'
    ];
    protected $validationMessages = [
        'code' => [
            'required' => 'رمز القسم مطلوب',
            'max_length' => 'رمز القسم لا يمكن أن يزيد عن 50 حرف',
            'is_unique' => 'رمز القسم موجود مسبقاً'
        ],
        'floor_id' => [
            'required' => 'الطابق مطلوب',
            'integer' => 'الطابق يجب أن يكون رقم صحيح',
            'is_not_unique' => 'الطابق المحدد غير موجود'
        ]
    ];
}
