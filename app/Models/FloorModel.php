<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Floor;

class FloorModel extends Model
{
    protected $table = 'floor';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Floor::class;
    protected $allowedFields = ['code', 'building_id'];
    protected $useTimestamps = false; // No timestamps in migration

    protected $validationRules = [
        'code' => 'required|max_length[50]|is_unique[floor.code,id,{id}]',
        'building_id' => 'required|integer|is_not_unique[building.id]'
    ];
    protected $validationMessages = [
        'code' => [
            'required' => 'رمز الطابق مطلوب',
            'max_length' => 'رمز الطابق لا يمكن أن يزيد عن 50 حرف',
            'is_unique' => 'رمز الطابق موجود مسبقاً'
        ],
        'building_id' => [
            'required' => 'المبنى مطلوب',
            'integer' => 'المبنى يجب أن يكون رقم صحيح',
            'is_not_unique' => 'المبنى المحدد غير موجود'
        ]
    ];
}
