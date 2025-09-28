<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\MinorCategory;

class MinorCategoryModel extends Model
{
    protected $table = 'minor_category';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = MinorCategory::class;
    protected $allowedFields = ['name', 'major_category_id'];
    protected $useTimestamps = true;


    protected $validationRules = [
        'name' => 'required|max_length[255]|is_unique[minor_category.name,id,{id}]',
        'major_category_id' => 'required|integer|is_not_unique[major_category.id]'
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'اسم التصنيف الفرعي مطلوب',
            'max_length' => 'اسم التصنيف الفرعي لا يمكن أن يزيد عن 255 حرف',
            'is_unique' => 'اسم التصنيف الفرعي موجود مسبقاً'
        ],
        'major_category_id' => [
            'required' => 'التصنيف الرئيسي مطلوب',
            'integer' => 'التصنيف الرئيسي يجب أن يكون رقم صحيح',
            'is_not_unique' => 'التصنيف الرئيسي المحدد غير موجود'
        ]
    ];
}
